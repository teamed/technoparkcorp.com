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
                "Validation failure in type " . get_class($this)
            );
        }
        $this->_addChange($value);
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
            if (is_null($change->date))
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
            // maybe this is the change made during this script?
            // not saved yet to tracker
            if (is_null($change->date))
                continue;
            if (is_null($date) || $date->isEarlier($change->date))
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
            // maybe this is the change made during this script?
            // not saved yet to tracker
            if (is_null($change->date))
                continue;
            if (is_null($date) || $date->isEarlier($change->date)) {
                $date = $change->date;
                $author = $change->author;
            }
        }
        return $author;
    }
    
    /**
     * Get current value
     *
     * @return mixed NULL means that there are no changed yet, no value yet
     **/
    public function getValue()
    {
        if (!count($this->_changes)) {
            return null;
        }

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
     * @param Zend_Date|null When changes were made, NULL if now
     * @return void
     **/
    protected function _addChange($value, $author = null, Zend_Date $date = null)
    {
        if (!is_null($author)) {
            validate()
                ->emailAddress($author, array(), "Invalid email: '{$author}'");
        }
        
        // do NOT duplicate changes
        if ($this->getValue() === $value)
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
