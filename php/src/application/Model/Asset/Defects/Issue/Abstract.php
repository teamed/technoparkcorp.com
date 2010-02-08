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
 * One abstract issue in issue-tracking system
 *
 * @package Model
 */
abstract class Model_Asset_Defects_Issue_Abstract
{

    /**
     * Tracker
     * 
     * @var Model_Asset_Defects_Abstract
     */
    protected $_tracker = null;

    /**
     * Code of the issue, unique in the tracker (NULL if ID is specified)
     * 
     * @var string
     * @see $this->_id
     */
    protected $_code = null;
    
    /**
     * Changelog
     *
     * @var Model_Asset_Defects_Issue_Changelog_Changelog
     * @see _getChangelog()
     */
    protected $_changelog = null;

    /**
     * Unique ID of the ticket in tracker
     *
     * @var integer
     * @see $this->_code
     */
    protected $_id = null;
    
    /**
     * Constructor
     *
     * @param Model_Asset_Defects_Abstract Tracker instance
     * @param string Unique code of the issue
     * @param integer ID of the ticket
     * @return void
     * @throws Exception If parameters are invalid
     */
    public function __construct(Model_Asset_Defects_Abstract $tracker, $code, $id = null) 
    {
        validate()
            ->true(
                is_null($id) || is_integer($id), 
                "Issue ID should be NULL or integer, {$id} provided"
            )
            ->true(
                (empty($code) && !is_null($id)) || (is_null($id) && is_string($code)), 
                "Either CODE ({$code}) or ID ({$id}) please"
            );
        
        $this->_tracker = $tracker;
        $this->_code = $code;
        $this->_id = $id;
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct() 
    {
        try {
            if (isset($this->_changelog))
                $this->_saveChangelog();
        } catch (Exception $e) {
            FaZend_Log::err('failed to save changes to ticket #' . $this->_id);
        }
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
     * @throws Model_Asset_Defects_Issue_PropertyOrMethodNotFound
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise(
            'Model_Asset_Defects_Issue_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );        
    }

    /**
     * This issue really exist in tracker now?
     *
     * @return integer ID in tracker
     **/
    abstract public function exists();

    /**
     * Cost is estimated?
     *
     * @return boolean
     **/
    public function isCostEstimated() 
    {
        return $this->changelog->get('cost') && $this->changelog->get('cost')->getValue();
    }

    /**
     * Duration is estimated?
     *
     * @return boolean
     **/
    public function isDurationEstimated() 
    {
        return $this->changelog->get('duration') && $this->changelog->get('duration')->getValue();
    }

    /**
     * Is it closed?
     *
     * @return boolean
     **/
    public function isClosed() 
    {
        return ($this->changelog->get('status')->getValue() != Model_Asset_Defects_Issue_Changelog_Field_Status::OPEN);
    }

    /**
     * Is it assigned?
     *
     * @return boolean
     **/
    public function isAssigned() 
    {
        return (bool)$this->changelog->get('owner')->getValue();
    }

    /**
     * Re-assign
     *
     * @param string New email to assign
     * @return $this
     */
    public function reassign($email) 
    {
        $this->changelog->get('owner')->setValue($email);
        return $this;
    }

    /**
     * Say something to the ticket
     *
     * @param string New text to say
     * @return $this
     */
    public function say($msg) 
    {
        $this->changelog->get('comment')->setValue($msg);
        return $this;
    }

    /**
     * Send this message just once to the ticke
     *
     * @param string Code of the message
     * @param string Text of the message
     * @param integer|null How many days before we can ask again, NULL means - never ask again
     * @return boolean The ticket is alredy asked (FALSE) or asked now (TRUE)
     **/
    abstract public function askOnce($code, $text, $lag = null);
    
    /**
     * Get changelog for this issue
     *
     * @return Model_Asset_Defects_Issue_Changelog_Changelog
     * @uses $this->_changelog
     */
    protected function _getChangelog() 
    {
        if (!is_null($this->_changelog))
            return $this->_changelog;
            
        $this->_changelog = new Model_Asset_Defects_Issue_Changelog_Changelog();
        
        // load it with real-life data from tracker
        if ($this->exists())
            $this->_loadChangelog();
            
        return $this->_changelog;
    }

    /**
     * Load changelog
     *
     * @return void
     * @see _getChangelog()
     */
    abstract protected function _loadChangelog();

    /**
     * Save changelog
     *
     * @return void
     * @see __destruct()
     */
    abstract protected function _saveChangelog();

}
