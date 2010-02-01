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
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Simple html icon
 *
 * @package helpers
 */
class Helper_Icon extends FaZend_View_Helper
{

    /**
     * Builds the html icon
     *
     * @return string
     */
    public function icon($name)
    {
        $text = @file_get_contents(APPLICATION_PATH . '/views/icons/' . $name . '.txt');

        if (!$text)
            return "<b>missed [{$name}] icon</b>";

        $html = '<table class="icon" cellspacing="0" cellpadding="0">';
        foreach (explode("\n", $text) as $line) {
            $cols = str_split($line);
            $html .= '<tr>';
            foreach ($cols as $column) 
                $html .= '<td class="i' . (trim($column, "\t\n\r ") ? '1' : '0') . '"></td>';
            $html .= '</tr>';
        }

        return $html . '</table>';
    }

}
