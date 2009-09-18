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
     * Style
     *
     * @var string
     */
    protected $_style = 'snake';

    /**
     * Builds a collection of bars
     *
     * @return Helper_Bar
     */
    public function bar() {
        return $this;
    }

    /**
     * Set style
     *
     * @param string Style
     * @return $this
     */
    public function setStyle($style) {
        $this->_style = $style;
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

            $resolvedLink = Model_Pages::resolveLink($link['link']);

            // if this link is not allowed for current user
            if (!Model_Pages::getInstance()->isAllowed($resolvedLink))
                continue;

            $htmls[] = '<a href="' . $this->getView()->panelUrl($resolvedLink) . '" ' .
                'title="' . $this->getView()->escape($link['title'] . " ({$resolvedLink})") . '">' .
                $this->getView()->escape($link['title']) . '</a>';

        }

        if (!count($htmls))
            return '';

        return $this->{'_draw' . ucfirst($this->_style)}($htmls);
    }

    /**
     * Draw in SNAKE style
     *
     * @param array List of links
     * @return string
     */
    protected function _drawSnake(array $links) {
        return '<p>' . implode('&#32;&middot;&#32;', $links) . '</p>';
    }

    /**
     * Draw in STAIRS style
     *
     * @param array List of links
     * @return string
     */
    protected function _drawStairs(array $links) {
        return '<ul><li>' . implode('</li><li>', $links) . '</li></ul>';
    }

}
