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
 * One decision
 *
 * @package Model
 */
abstract class Model_Decision implements Model_Decision_Interface {

    /**
     * File name of the decision
     *
     * @var string
     */
    protected $_file;

    /**
     * Wobot that created this decision
     *
     * @var Model_Wobot
     */
    protected $_wobot;

    /**
     * Decision just made
     *
     * @var string|false
     */
    protected $_decision;

    /**
     * Protected constructor
     *
     * @param string File name of the class
     * @param Model_Wobot Wobot calling this decision
     * @return void
     */
    protected function __construct($file, Model_Wobot $wobot) {
        $this->_file = $file;
        $this->_wobot = $wobot;
    }

    /**
     * Create instance of this class, using the file name
     *
     * @param string File name of the decision file
     * @param Model_Wobot Wobot, the initiator
     * @return Model_Decision
     */
    public static function factory($file, Model_Wobot $wobot) {
        $className = pathinfo($file, PATHINFO_FILENAME);
        require_once $file;
        return new $className($file, $wobot);
    }

    /**
     * File name hash calculator
     *
     * @param string File name
     * @return string
     */
    public static function hash($file) {
        return pathinfo($file, PATHINFO_FILENAME) . '/' . md5($file);
    }
    
    /**
     * Return has of this particular decision
     *
     * The method is called from protocoller
     *
     * @return string
     */
    public function getHash() {
        return self::hash($this->_file);
    }

    /**
     * Make decision and protocol results
     *
     * @return string|false Result of decision made (FALSE = no decission)
     */
    public function make() {

        FaZend_Log::getInstance()->addWriter('Memory', 'decision');

        try {
            
            logg('Starting decision: ' . $this->_file);
            $decision = $this->_make();

        } catch (Exception $e) {
            // some error inside - we skip the process
            logg($e->getMessage());
            $decision = false;
        }
        
        $log = FaZend_Log::getInstance()->getWriterAndRemove('decision')->getLog();
        
        // protocol this decision
        Model_Decision_History::create($this->_wobot, $this, $decision, $log);
        
        return $decision;
        
    }

    /**
     * Decision making method
     *
     * The method is called when required. It should make all necessary
     * operations and return string message - what decision has been made.
     *
     * During the execution of the method you should protocol your decision
     * making process using $this->_log() method.
     *
     * If you return FALSE - it means that you didn't make any decision.
     *
     * @return string|false
     */
    abstract protected function _make();

}
