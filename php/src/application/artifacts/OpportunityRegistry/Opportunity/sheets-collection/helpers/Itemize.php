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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id: Vision.php 651 2010-02-10 17:50:05Z yegor256@yahoo.com $
 *
 */

/**
 * TeX list
 *
 * @package Artifacts
 */
class Sheet_Helper_Itemize extends FaZend_View_Helper
{

    /**
     * List in TeX
     *
     * @return string
     * @throws Sheet_Helper_Itemize_InvalidStyleException
     */
    public function itemize($collection, $style = 'itemize') 
    {
        switch ($style) {
            case 'itemize':
            case 'description':
            case 'enumerate':
                if (!$collection) {
                    return "\\textit{empty...}";
                }

                $tex = "\\begin{{$style}}\n";
                foreach ($collection as $item) {
                    $tex .= sprintf(
                        "\t\item[%s] %s\n",
                        $this->getView()->tex($item['name']),
                        $this->getView()->tex($item['value'])
                    );
                }
                $tex .= "\\end{{$style}}";
                break;
        
            case 'inline':
                if (!$collection) {
                    return "$\dots$";
                }
                
                $items = array();
                foreach ($collection as $item) {
                    $items[] = $this->getView()->tex($item['name']);
                }
                if (count($items) > 1) {
                    $items[count($items)-1] = 'and ' . $items[count($items)-1];
                }
                $tex = implode(((count($items) > 2) ? ',' : false) . "\n\t", $items) . ' ';
                break;
        
            default:
                FaZend_Exception::raise(
                    'Sheet_Helper_Itemize_InvalidStyleException', 
                    "Style '{$style}' is unknown"
                );
            
        }
        return $tex;
    }

}
