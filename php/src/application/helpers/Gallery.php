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
 * Gallery of objects
 *
 * @package helpers
 */
class Helper_Gallery extends FaZend_View_Helper {

    /**
     * Icon name
     *
     * @var string
     */
    protected $_icon = 'document';

    /**
     * Source of data
     *
     * @var array
     */
    protected $_source;

    /**
     * Link to be added to each element
     *
     * @var string
     */
    protected $_link = null;

    /**
     * Title to show (name of property)
     *
     * @var string
     */
    protected $_title;

    /**
     * Builds the gallery object
     *
     * @return Helper_Table
     */
    public function gallery() {
        return $this;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function __toString() {
        $html = '<div class="gallery">';
        
        foreach ($this->_source as $key=>$element) {

            $link = Model_Pages::resolveLink($this->_link, $element, $key);

            // if this link is not allowed for current user
            if (!Model_Pages::getInstance()->isLinkAllowed($link))
                continue;

            $html .= "<div class='element'>" .
                $this->getView()->icon($this->_icon);

            if (isset($this->_link)) {
                $html .= '<br/>' .
                    "<a href='" . $link . "'>" .
                    $this->getView()->escape(($this->_title == '__key' ? $key : $element->{$this->_title})) . "</a>";
            }

            $html .= '</div>';
        }

        // configure CSS for this gallery
        $this->getView()->includeCSS('helper/gallery.css');

        return $html . '</div>';

    }

    /**
     * Set data source
     *
     * @param Iterator
     * @return Helper_Table
     */
    public function setSource(Iterator $iterator) {
        $this->_source = $iterator;
        return $this;
    }

    /**
     * Set the name of icon to show
     *
     * @param string Name of icon
     * @return Helper_Table
     */
    public function setIcon($icon) {
        $this->_icon = $icon;
        return $this;
    }

    /**
     * Set the link
     *
     * @param string Link to use for each element
     * @return Helper_Table
     */
    public function setLink($link) {
        $this->_link = $link;

        $matches = array();
        if (preg_match('/\{(.*?)\}/', $this->_link, $matches))
            $this->setTitle($matches[1]);

        return $this;
    }

    /**
     * Set the name of icon to show
     *
     * @param string Name of title property
     * @return Helper_Table
     */
    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

}
