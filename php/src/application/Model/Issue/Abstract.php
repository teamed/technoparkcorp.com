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
 * One abstract issue in issue-tracking system
 *
 * @package Model
 */
abstract class Model_Issue_Abstract {

    /**
     * Tracker
     * 
     * @var Model_Issue_Tracker_Abstract
     */
    protected $_tracker = null;

    /**
     * Code of the issue, unique in the tracker
     * 
     * @var string
     */
    protected $_code;
    
    /**
     * List of messages in this trac
     *
     * @var Model_Issue_Message_Abstract[]
     */
    protected $_messages;

    /**
     * List of fields
     *
     * @var string[]
     */
    protected $_fields = array();

    /**
	 * Constructor
     *
     * @param Model_Issue_Tracker_Abstract Tracker instance
     * @param string Unique code of the issue
     * @return void
     */
	public function __construct(Model_Issue_Tracker_Abstract $tracker, $code) {
	    $this->_tracker = $tracker;
	    $this->_code = $code;
    }

    /**
     * Set field value
     *
     * @return string
     **/
    public function setField($name, $value) {
        $this->_fields[$name] = $value;
        return $this;
    }

    /**
     * Get field value
     *
     * @param string Name of the field
     * @return string
     **/
    public function getField($name) {
        return $this->_fields[$name];
    }

    /**
     * Return code of the issue
     *
     * @return string
     **/
    public function getCode() {
        return $this->_code;
    }

    /**
     * This issue really exist in tracker now?
     *
     * @return void
     **/
    public function exists() {
        return $this->_tracker->issueExists($this);
    }

    /**
     * Get list of messages
     *
     * @return Model_Issue_Message_Abstract
     **/
    public function getMessages() {
        if (!isset($this->_messages))
            $this->_messages = $this->_tracker->getIssueMessages($this);
        return $this->_messages;
    }

    /**
     * Make sure it exists in tracker
     *
     * @return void
     **/
    public function makeAlive() {
        return $this->_tracker->makeIssueAlive($this);
    }

}
