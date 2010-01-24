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
 * Dynamic link
 *
 * @package helpers
 */
class Helper_Link extends FaZend_View_Helper {

    /**
     * Builds a link
     *
     * @param string Link to use
     * @param string|null Title to show in HTML (NULL means that we need just HREF)
     * @param boolean Build it in form of a paragraph
     * @return Helper_Table
     */
    public function link($link, $title = null, $inPar = true) {
        $resolvedLink = Model_Pages::resolveLink($link);

        // if this link is not allowed for current user
        if (!Model_Pages::getInstance()->isAllowed($resolvedLink)) {
            if (is_null($title))
                return '#unresolved';
            return ($inPar ? '' : '...');
        }
        
        $uri = $this->getView()->panelUrl($resolvedLink);
        if (is_null($title))
            return $uri;

        $html = '<a href="' . $uri . '">' .
        $this->getView()->escape($title) . '</a>';

        return ($inPar ? '<p>' . $html . '</p>' : $html);
    }

}
