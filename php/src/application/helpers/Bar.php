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
 * Collection of links
 *
 * @package helpers
 */
class Helper_Bar extends FaZend_View_Helper
{

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
    public function bar()
    {
        return $this;
    }

    /**
     * Set style
     *
     * @param string Style
     * @return $this
     */
    public function setStyle($style)
    {
        $this->_style = $style;
        return $this;
    }

    /**
     * Add collection of links
     *
     * It should be an associative array with keys as links
     * and values as labels
     *
     * @param array|Iterator collection to add
     * @return $this
     */
    public function addCollection($collection) 
    {
        foreach ($collection as $id=>$item) {
            $this->addLink($id, $item);
        }
        return $this;
    }

    /**
     * Add new link
     *
     * @param string Link
     * @param string Label
     * @return $this
     */
    public function addLink($link, $title = null)
    {
        $this->_links[] = array(
            'link'=>$link,
            'title'=>(is_null($title) ? $link : $title)
        );
        return $this;
    }

    /**
     * Render the helper
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string)$this->_render();
        } catch (Exception $e) {
            return get_class($this) . ' throws ' . get_class($e) . ': ' . $e->getMessage();
        }
    }

    /**
     * Render the helper
     *
     * @return string
     */
    protected function _render()
    {
        $htmls = array();

        foreach ($this->_links as $link) {
            $resolvedLink = Model_Pages::resolveLink($link['link']);

            // if this link is not allowed for current user
            if (!Model_Pages::getInstance()->isAllowed($resolvedLink))
                continue;

            $htmls[] = sprintf(
                '<a href="%s" title="%s">%s</a>',
                $this->getView()->panelUrl($resolvedLink),
                $this->getView()->escape($link['title'] . " ({$resolvedLink})"),
                $this->getView()->escape($link['title'])
            );
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
    protected function _drawSnake(array $links)
    {
        return sprintf(
            '<p>%s</p>',
            implode('&#32;&middot;&#32;', $links)
        );
    }

    /**
     * Draw in STAIRS style
     *
     * @param array List of links
     * @return string
     */
    protected function _drawStairs(array $links)
    {
        return sprintf(
            '<ul><li>%s</li></ul>',
            implode('</li><li>', $links)
        );
    }

}
