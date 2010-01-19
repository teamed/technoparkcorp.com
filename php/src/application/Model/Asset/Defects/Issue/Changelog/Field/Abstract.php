<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One changelog field
 *
 * @package Model
 */
abstract class Model_Asset_Defects_Issue_Changelog_Field_Abstract
{

    /**
     * List of values
     *
     * ->date
     * ->author
     * ->value
     *
     * @var FaZend_StdObject[]
     */
    protected $_changes = array();

    /**
     * Get all types of fields
     *
     * @return string[]
     **/
    public static function getAllTypes()
    {
        $list = array();
        foreach (glob(dirname(__FILE__) . '/*.php') as $file) {
            $type = strtolower(pathinfo($file, PATHINFO_FILENAME));
            if ($type == 'abstract')
                continue;
            $list[] = $type;
        }
        return $list;
    }

    /**
     * Set new value
     *
     * @param mixed Value to set
     * @return $this
     * @throws Model_Asset_Defects_Issue_Changelog_Field_CantChange
     **/
    public function setValue($value)
    {
        if (!$this->_validate($value)) {
            FaZend_Exception::raise(
                'Model_Asset_Defects_Issue_Changelog_Field_CantChange',
                "You can't change value of type " . get_class($this)
            );
        }
        $this->_addChange($value, null, null);
    }

    /**
     * Set new value
     *
     * @param mixed Value to set
     * @param string Email of the author
     * @param Zend_Date When changes were made
     * @return $this
     **/
    public function load($value, $author, Zend_Date $date)
    {
        $this->_validate($value);
        $this->_addChange($value, $author, $date);
    }

    /**
     * Was it changed during this session?
     *
     * @return boolean
     **/
    public function wasChanged()
    {
        // empty DATE means that the value was changed
        // in THIS script, and is waiting for deployment
        // to tracker
        foreach ($this->_changes as $change) {
            if (!$change->date)
                return true;
        }
        return false;
    }

    /**
     * Get the date of last change
     *
     * @return Zend_Date
     **/
    public function getLastDate()
    {
        $date = null;
        foreach ($this->_changes as $change) {
            if (is_null($date) || $date->isLater($change->date))
                $date = $change->date;
        }
        return $date;
    }
    
    /**
     * Get the author of the last change
     *
     * @return string Email of the author
     **/
    public function getLastAuthor()
    {
        $date = $author = null;
        foreach ($this->_changes as $change) {
            if (is_null($date) || $date->isLater($change->date)) {
                $date = $change->date;
                $author = $change->author;
            }
        }
        return $author;
    }
    
    /**
     * Get current value
     *
     * @return mixed
     **/
    public function getValue()
    {
        if (!count($this->_changes))
            return null;

        return $this->_changes[count($this->_changes)-1]->value;
    }
    
    /**
     * Get a list of changes
     *
     * @return FaZend_StdObject[]
     **/
    public function getChanges()
    {
        return $this->_changes;
    }

    /**
     * Add one change
     *
     * @param mixed Value to set
     * @param string Email of the author
     * @param Zend_Date When changes were made
     * @return void
     **/
    protected function _addChange($value, $author, Zend_Date $date)
    {
        if ($this->getValue() == $value)
            return;
        $this->_changes[] = FaZend_StdObject::create()
            ->set('author', $author)
            ->set('date', $date)
            ->set('value', $value);
    }

    /**
     * Validate new value
     *
     * @param mixed Value to set
     * @return void
     * @throws Exception if failed
     **/
    abstract protected function _validate($value);

}
