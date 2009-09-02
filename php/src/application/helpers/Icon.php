<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
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
class Helper_Icon extends FaZend_View_Helper {

    /**
     * Builds the html icon
     *
     * @return string
     */
    public function icon($name) {

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
