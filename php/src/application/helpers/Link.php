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
 * Dynamic link
 *
 * @package helpers
 */
class Helper_Link extends FaZend_View_Helper {

    /**
     * Builds a link
     *
     * @param string Link to use
     * @param string Title to show in HTML
     * @param boolean Build it in form of a paragraph
     * @return Helper_Table
     */
    public function link($link, $title, $inPar = true) {

        $resolvedLink = Model_Pages::resolveLink($link);

        // if this link is not allowed for current user
        if (!Model_Pages::getInstance()->isAllowed($resolvedLink))
            return ($inPar ? '' : '...');

        $html = '<a href="' . $this->getView()->panelUrl($resolvedLink) . '">' .
        $this->getView()->escape($title) . '</a>';

        return ($inPar ? '<p>' . $html . '</p>' : $html);
    }

}
