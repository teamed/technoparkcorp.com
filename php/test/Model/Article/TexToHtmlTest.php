<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';

class Model_Article_TexToHtmlTest extends AbstractTest 
{

    public function provideTex()
    {
        return array(
            array(
                "
                \\textbf{Test} it properly: \\begin{itemize} \\item Test \\item Test B \\end{itemize}
                "
            ),
            array(
                "
                This is paragraph ``no.1'' \n
                \\textbf{Test} it properly:\n
                \\begin{itemize} \\item Single \\end{itemize}\n\n
                \\begin{itemize} \\item Test \\item Test B \\end{itemize}\n\n
                How about this:\n\\begin{inparaenum}[\\itshape a\\upshape)]\\item Works?\\end{inparaenum}\n
                Last para...
                "
            )
        );
    }

    /**
     * @dataProvider provideTex
     */
    public function testValidityOfXml($tex) 
    {
        $t = new Model_Article_TexToHtml();
        $html = $t->convert($tex);
        $html = html_entity_decode($html, 0, 'UTF-8');
        $xml = simplexml_load_string('<?xml version="1.0"?><root>' . $html . '</root>');
        var_dump($html);
        $this->assertNotEquals(false, $xml, $html);
    }
    
}