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
class Helper_Table extends FaZend_View_Helper
{

    /**
     * Columns to show
     *
     * @var array
     */
    protected $_columns;

    /**
     * Links with names
     *
     * Associative array, where key = name of link, value = mnemo of link
     *
     * @var array
     */
    protected $_links;

    /**
     * Name of column that was added lately
     *
     * @var string
     */
    protected $_predecessor;

    /**
     * Html table helper instance
     *
     * @var FaZend_View_Helper_HtmlTable
     */
    protected $_table;
    
    /**
     * Id of the next table to show
     *
     * @var integer
     */
    protected static $_tableId = 0;

    /**
     * Builds the html table
     *
     * @return Helper_Table
     */
    public function table()
    {
        // just get next table
        $this->_table = $this->getView()->htmlTable(self::$_tableId++);
        $this->_links = array();
        $this->_columns = array();
        $this->_predecessor = false;
        
        return $this;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
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
     * Converts it to HTML
     *
     * @return string HTML
     */
    protected function _render()
    {
        $this->_table->showColumns($this->_columns);
        $this->_table->setNoDataMessage('');

        $html = $this->_table->__toString();
        if (!$html)
            return '';

        return '<p>' . $html . '</p>' . $this->getView()->paginator;
    }

    /**
     * Set data source
     *
     * @param Iterator
     * @return Helper_Table
     */
    public function setSource(Iterator $iterator)
    {
        validate()
            ->instanceOf($iterator, 'Iterator', "Source should be an instance of Iterator");
            
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
    public function addColumn($name, $header = null)
    {
        // remember the name of this column added
        $this->_columns[] = $name;

        // add column to the htmlTable()
        $this->_table->addColumn($name, $this->_predecessor);

        // reconfigure header, if the name is given
        if (!is_null($header))
            $this->_table->setColumnTitle($name, $header);

        // set predecessor to make sure we allocate them consequently
        $this->_predecessor = $name;

        // return itself, to allow fluent interface
        return $this;
    }

    /**
     * Add new option
     *
     * @param string Name of the option
     * @param string Link to the operation
     * @return Helper_Table
     */
    public function addOption($name, $link)
    {
        // this params will be sent to the htmlTable() helper
        $urlParams = array('doc'=>array($this, 'resolveDocumentName'));

        // add the link to this helper
        $this->_links[$name] = $link;

        // user func call params
        $params = array($name, null, null, $urlParams, 'panel', true, false);

        // it will automatically understand whether the option should
        // stay in 'OPTIONS' column, or should be attached to the data column
        if (in_array($name, $this->_columns))
            $func = 'addColumnLink';
        else
            $func = 'addOption';

        // attach option to the htmlTable helper
        call_user_func_array(array($this->_table, $func), $params);

        // return itself, to allow fluent interface
        return $this;
    }

    /**
     * Add converter to the column
     *
     * @param string Class name
     * @param string|null Method name
     * @return Helper_Table
     */
    public function addConverter($class, $method = null)
    {
        // add column to the htmlTable()
        $this->_table->addConverter($this->_predecessor, $class, $method);
        return $this;
    }
    
    /**
     * Add formatter to the column
     *
     * @param string Condition
     * @param string|null Style
     * @return Helper_Table
     */
    public function addFormatter($condition, $style = null)
    {
        // add column to the htmlTable()
        $this->_table->addFormatter($this->_predecessor, $condition, $style);
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
    public function resolveDocumentName($name, $row, $key)
    {
        return Model_Pages::resolveLink($this->_links[$name], $row, $key);
    }

}
