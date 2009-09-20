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
 * One abstract order
 *
 * @package Model
 */
abstract class Model_Order_Abstract {

    /**
     * Initiator of the order
     *
     * @var Model_Decision
     */
    protected $_decision;

    /**
     * Unique ID of the order
     *
     * @var string
     */
    protected $_id;

    /**
     * Constructor
     *
     * @param Model_Decision Initiator of the order
     * @param string Id of the order
     * @return void
     */
    public function __construct(Model_Decision $decision, $id) {
        $this->_decision = $decision;
        $this->_id = $id;
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
     * Execute order and return text summary of its execution
     *
     * @return string
     **/
    abstract public function execute();

}
