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
class Model_Issue_Abstract {

    /**
     * Id of the issue, unique in the tracker
     * 
     * @var string
     */
    protected $_id;

    /**
	 * Constructor
     *
     * @param string Unique id of the issue
     * @return void
     */
	public function __construct($id) {
	    $this->_id = $id;
    }

    /**
	 * Destructor, initiates the saving operation
     *
     * @return void
     */
	public function __destruct() {
	    $this->_save();
    }

    /**
     * Set local variables
     *
     * @param string Name of the variable
     * @param mixed Value of it
     * @return void
     **/
    public function __call($name, $args) {
        $matches = array();
        if (preg_match('/^set(.*)$/', $name, $matches) {
            $matches[1][0] = strtolower($matches[1][0]);
            $this->{'_' . matches[1][0]} = array_shift($args);
            return $this;
        }
    }
    
    /**
     * Make sure the issue exists. If not - create it right now
     *
     * @return $this
     **/
    abstract public function makeAlive();
    
    /**
     * Make sure the issue exists AND is open right now. If not, do the necessary actions.
     *
     * @return $this
     **/
    abstract public function makeOpen();
    
    /**
     * Save all changes made
     *
     * @return $this
     **/
    abstract protected function _save();
    
}
