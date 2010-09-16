<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * TeX to HTML converter
 *
 * @package cls
 */
class Model_Article_TexToHtml
{

    /**
     * REGEX to convert
     *
     * @var string[]
     */
    protected $_lexems = array(
        // kill comments
        '/\s%\s.*\n/' => ' ',

        // convert \\ into <br/>
        '/\\\\{2}/m' => '<br/>',
    
        // remove \r
        '/\r/m' => "\n",

        // group paragraphs together
        '/[\x20\t]+/m' => ' ',
        '/\x20+\n/m' => "\n",
        '/\n\x20+/m' => "\n",
        '/\n{2,}/m' => "\n\n",
        '/([^\n])\n([^\n])/m' => '${1} ${2}',
        '/([^\n])\n\n([^\n])/m' => "\${1}\n\n\n\n\${2}",
        '/\n\n(.*)\n\n/mU' => '<p>${1}</p>',
        '/\n/m' => ' ',

        // quotes
        "/``/" => '&laquo;',
        "/\\'\\'/" => '&raquo;',
        "/~/" => '&nbsp;',
        
        // dashes
        "/---/" => '&mdash;',
        "/--/" => '&ndash;',

        // special chars
        "/\\\\%/" => '&#37;',
        "/\\\\_/" => '&#95;', // underscore
        "/\\\\&/" => '&amp;',
        "/\\\\#/" => '&#35;',
        "/\\\\\\$/" => '&#36;',
        "/\\$\\\\geq\\$/" => '&gt;=',
        "/\\$<\\$/" => '&lt;',

        // font formatting
        '/\\\\textbf\{(.*?)\}/' => '<b>${1}</b>',
        '/\\\\textit\{(.*?)\}/' => '<i>${1}</i>',
        '/\\\\texttt\{(.*?)\}/' => '<tt>${1}</tt>',
        '/\\\\emph\{(.*?)\}/' => '<i>${1}</i>',

        // coloring
        '/\\\\colorbox\{(.*?)\}\{(.*?)\}/' => "<span style='background-color: yellow;'>\${2}</span>",

        '/\\\\tablehead\{.*?\}/' => '',

        '/\\\\ragged(right|left)/' => '',
        '/\\\\clearpage/' => '',
        '/\\\\label\{(.*?)\}/' => "<a name='\${1}'></a>",
        '/\\\\hyperref\[(.*?)\]\{(.*?)\}/' => "<a href='#\${1}'>\${2}</a>",
        '/\\\\(page)?ref\{.*?\}/' => '?',

        '/\s*\\\\item\s(.*?)(?=\\\\item|\\\\end{(?:itemize|enumerate|inparaenum)})/' => '<li>${1}</li>',
        '/\s*\\\\end\{itemize\}/' => '</ul>',
        '/\s*\\\\end\{enumerate\}/' => '</ol>',
        '/\s*\\\\begin\{itemize\}/' => '<ul>',
        '/\s*\\\\begin\{enumerate\}/' => '<ol>',
        '/\s*\\\\begin\{inparaenum\}\[.*?\]/' => '<ol class="inparaenum">',
        '/\s*\\\\end\{inparaenum\}/' => '</ol>',

        // sections
        '/\\\\section\*?\{(.*?)\}/' => '<h2>${1}</h2>',
        '/\\\\subsection\*?\{(.*?)\}/' => '<h3>${1}</h3>',
        '/\\\\subsubsection\*?\{(.*?)\}/' => '<h4>${1}</h4>',

        // verbatim to PRE
        '/\\\\begin\{verbatim\}(.*?)\\\\end\{verbatim\}/' => '<pre>${1}</pre>',

        // paragraphs
        '/\\\\begin\{flushleft\}(.*?)\\\\end\{flushleft\}/' => '${1}',

        // hyper refs
        '/\\\\href\{(.*?)\}\{(.*?)\}/' => "<a href='\${1}'>\${2}</a>",

        '/\\\\rule\{(.*?)\}\{(.*?)\}/' => "<hr style='width: \${1}; height: \${2};'/>",

        // tikz picture
        '/\s*(\\\\begin\{tikzpicture\}.*?\\\\end\{tikzpicture\})/' => '<tikz>${1}</tikz>',
        '/\s*(\\\\begin\{(equation\*?|gather\*?)\}.*?\\\\end\{(equation\*?|gather\*?)\})/' => '<tikz>${1}</tikz>',

        '/\\\\begin\{center\}(.*?)\\\\end\{center\}/' => '<center>${1}</center>',
        '/\\\\(noindent|small|large|footnotesize|scriptsize|displaystyle)/' => '',
        '/\\\\setstretch\{[\d\.]+\}/' => '',
        '/\\\\(makebox|mbox)(\[[\\\\\w\d\.]+\])?\s?\{(.*?)\}/' => '${3}',

        // kill the junk
        '/\\\\begin\{wideTable\}(.*?)\\\\end\{wideTable\}/' => '${1}',

        // biblio references
        '/\\\\begin\{thebibliography\}\{\d+\}(.*)\\\\end\{thebibliography\}/' => 
            '<h2>References</h2>${1}',
        '/\\\\bibitem\[.*?\]\{([\w\d]+)\}/' => '[<span style="font-variant: small-caps;">${1}</span>]',
        '/\\\\cite(\[(.*?)\])?\{([\w\d]+)\}/' => '[<span style="font-variant: small-caps;">${3} ${2}</span>]',

        // formulas
        '/\$(.*?)\$/' => '&xi;',

        // clear stupid elements
        '/<p><\/p>/' => '',
        '/<p>\s/' => '<p>',
        '/\s<\/p>/' => '</p>',
    );

    /**
     * Sensitive blocks to protect
     *
     * @var string[]
     */
    protected $_sensitives = array(
        '/\\\\begin\{tikzpicture\}(.*?)\\\\end\{tikzpicture\}/ms',
        '/\\\\begin\{gather\*?\}(.*?)\\\\end\{gather\*?\}/ms',
    );

    /**
     * Convert it now
     *
     * @return string
     */
    public function convert($text)
    {
        $result = $this->_encodeSensitiveBlocks($text);

        $structures = array(
            '/\\\\begin\{(mpx)?tabular[\*x]?\}\s?\{[\|\>\\\<\{\}\d\.\w]+\}(.*?)\\\\end\{(mpx)?tabular[\*x]?\}/ms' =>
                '_convertTabular("${2}")',
        );

        // complex structures
        foreach ($structures as $preg=>$func) {
            $matches = array();
            if (!preg_match_all($preg, $result, $matches)) {
                continue;
            }

            foreach ($matches[0] as $id=>$match) {
                // replace all ${x} to real values from string
                $vars = array();
                if (preg_match_all('/\$\{(\d)\}/', $func, $vars)) {
                    foreach ($vars[1] as $varNumber) {
                        $replacedFunc = str_replace(
                            '${' . $varNumber . '}', 
                            addslashes($matches[$varNumber][$id]), 
                            $func
                        );
                    }
                }    

                // execute the resulted function
                $replacement = "[error in '{$func}']";
                eval("\$replacement = \$this->{$replacedFunc};");

                $result = str_replace($match, $replacement, $result);
            }    
        }    

        // simple convertion - the result is a long line without spaces
        $result = trim(
            preg_replace(
                array_keys($this->_lexems), 
                array_values($this->_lexems), 
                "\n\n" . $result . "\n\n"
            )
        );

        return $this->_decodeSensitiveBlocks($result);
    }

    /**
     * Convert \begin{tabular}\end{tabular} into <TABLE>
     *
     * @param string Text between \begin{tabular}{xxx} and \end{tabular}
     * @return string
     */
    protected function _convertTabular($content)
    {
        // $content = preg_replace(
        //     array(
        //         '/\s?\\\\hline\s?/sm',
        //     ), 
        //     array(
        //         "<tr style='height:1px; background-color: black;'></tr>",
        //     ), $content);

        $content = preg_replace(
            array(
                '/\s?\\\\hline\s?/sm',
            //    '/\s?\\\\tablehead\{.*?\}\s?/sm'
            ), 
            array(
                '\\hline\\\\\\\\',
            //    '',
            ), 
            $content
        );

        $html = '<table>';

        $lines = explode('\\\\', $content);

        foreach ($lines as $line) {
            $line = trim($line, " \t\n\r");

            if (!$line)
                continue;

            if ($line == '\\hline') {
                $html .= "<tr><td colspan='100' style='height: 1px; padding: 0px; background-color: " .
                Model_Colors::BLACK . "'></td></tr>";
                continue;
            }    

            $html .= '<tr>';
            $columns = explode('&', $line);
            foreach ($columns as $column) {
                $column = trim($column);

                $matches = array();
                if (preg_match('/\\\\multicolumn\{(\d+)\}\{.*?\}\{(.*?)\}/', $column, $matches))
                    $html .= "<td colspan='{$matches[1]}'>{$matches[2]}</td>";
                else
                    $html .= "<td>$column</td>";
            }
            $html .= '</tr>';
        }    

        $html .= '</table>';

        return $html;

    }

    /**
     * Encode(base64) sensitive blocks
     *
     * @param string TeX
     * @return string
     */
    protected function _encodeSensitiveBlocks($txt, $decode = false)
    {
        foreach ($this->_sensitives as $preg) {
            $matches = array();
            if (!preg_match_all($preg, $txt, $matches)) {
                continue;
            }

            foreach ($matches[0] as $key=>$match) {
                $txt = str_replace(
                    $match, 
                    str_replace(
                        $matches[1][$key], 
                        $decode ? base64_decode($matches[1][$key]) : base64_encode($matches[1][$key]), 
                        $match
                    ), 
                    $txt
                );    
            }

        }
        return $txt;
    }

    /**
     * Decode(base64) sensitive blocks
     *
     * @param string TeX
     * @return string
     */
    protected function _decodeSensitiveBlocks($txt)
    {
        return $this->_encodeSensitiveBlocks($txt, true);
    }

}
