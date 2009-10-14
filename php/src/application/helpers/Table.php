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
 * Table
 *
 * @package helpers
 */
class Helper_Table extends FaZend_View_Helper {

    /**
     * Columns to show
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * Links with names
     *
     * Associative array, where key = name of link, value = mnemo of link
     *
     * @var array
     */
    protected $_links = array();

    /**
     * Name of column that was added lately
     *
     * @var string
     */
    protected $_predecessor = false;

    /**
     * Builds the html table
     *
     * @return Helper_Table
     */
    public function table() {
        return $this;
    }

    /**
     * Get htmlTable helper
     *
     * @return value
     */
    public function __get($name) {
        if ($name == '_table')
            return $this->getView()->htmlTable('panel2');
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function __toString() {

        $this->_table->showColumns($this->_columns);

        return 
            '<p>' . $this->_table->__toString() . '</p>' .
            $this->getView()->paginator;
    }

    /**
     * Set data source
     *
     * @param Iterator
     * @return Helper_Table
     */
    public function setSource(Iterator $iterator) {

        FaZend_Paginator::addPaginator($iterator, $this->getView(), 1, 'paginator');

        // in other words - NO paging
        $this->getView()->paginator->setItemCountPerPage(1000);
        
        $this->_table->setPaginator($this->getView()->paginator);
        return $this;
    }

    /**
     * Add new column
     *
     * @param string|false Name of the object property to get, false = KEY
     * @param string Header to show
     * @return Helper_Table
     */
    public function addColumn($name, $header = null) {
        $this->_columns[] = $name;

        $this->_table->addColumn($name, $this->_predecessor);

        if (!is_null($header))
            $this->_table->setColumnTitle($name, $header);

        $this->_predecessor = $name;

        return $this;
    }

    /**
     * Add new option
     *
     * @param string Name of the option
     * @param string Link to the operation
     * @return Helper_Table
     */
    public function addOption($name, $link) {
        $urlParams = array('doc'=>array($this, 'resolveDocumentName'));

        $this->_links[$name] = $link;

        // user func call params
        $params = array($name, null, null, $urlParams, 'panel', true, false);

        if (in_array($name, $this->_columns))
            $func = 'addColumnLink';
        else
            $func = 'addOption';

        call_user_func_array(array($this->_table, $func), $params);

        return $this;
    }

    /**
     * Resolve document name by data
     *
     * This method is called by FaZend_View_Helper_HtmlTable when configured
     * by addOption() method in this class.
     *
     * @param string Name of the link/column
     * @param array Row from the table
     * @param mixed Key of the row
     * @return string
     */
    public function resolveDocumentName($name, $row, $key) {
        return Model_Pages::resolveLink($this->_links[$name], $row, $key);
    }

}
