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
 * Collection of links
 *
 * @package helpers
 */
class Helper_Bar extends FaZend_View_Helper {

    /**
     * Collection of links
     *
     * @var array
     */
    protected $_links = array();

    /**
     * Builds a collection of bars
     *
     * @return Helper_Bar
     */
    public function bar() {
        return $this;
    }

    /**
     * Add new link
     *
     * @param string Link
     * @param string Label
     * @return $this
     */
    public function addLink($link, $title) {
        $this->_links[] = array(
            'link'=>$link,
            'title'=>$title
        );
        return $this;
    }

    /**
     * Render the helper
     *
     * @return string
     */
    public function __toString() {

        $htmls = array();

        foreach ($this->_links as $link) {

            // if this link is not allowed for current user
            if (!Model_Pages::getInstance()->isLinkAllowed($link['link']))
                continue;

            $htmls[] = '<a href="' . $this->getView()->panelUrl($lnk = Model_Pages::resolveLink($link['link'])) . '" ' .
                'title="' . $this->getView()->escape($link['title'] . " ({$lnk})") . '">' .
                $this->getView()->escape($link['title']) . '</a>';

        }

        return '<p>' . implode(' &middot; ', $htmls) . '</p>';
    }

}
