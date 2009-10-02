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
 * Single work package
 *
 * @package Artifacts
 */
class theWorkPackage implements Model_Artifact_Stateless {
    
    /**
     * WBS, holder of this work package
     *
     * @var theWBS
     */
    protected $_wbs;
    
    /**
     * Unique code of this workpackage
     *
     * @var string
     */
    protected $_code;
    
    /**
     * Cost of work package, in USD dollars
     *
     * @var int
     */
    protected $_cost;
    
    /**
     * Statement of work
     *
     * @var string
     */
    protected $_SOW;
    
    /**
     * Create new work package from INI file
     *
     * @param theWBS Holder of this object
     * @param string Code of the work package
     * @param Zend_Config_Ini INI file with configuration
     * @return void
     **/
    public function __construct(theWBS $wbs, $code, Zend_Config_Ini $config) {
        $this->_wbs = $wbs;
        $this->_code = $code;
        $this->_loadIni($config);
    }
    
    /**
     * Get the code of this WP
     *
     * @return string
     **/
    public function getCode() {
        return $this->_code;
    }
    
    /**
     * Get the cost in USD of this WP
     *
     * @return integer
     **/
    public function getCost() {
        return $this->_cost;
    }
    
    /**
     * Get statement of work of this WP
     *
     * @return string
     **/
    public function getSOW() {
        return $this->_SOW;
    }
    
    /**
     * Convert INI file into internal variables
     *
     * @param Zend_Config_Ini INI file with configuration
     * @return void
     **/
    protected function _loadIni(Zend_Config_Ini $config) {
        $this->_parse($config);
        $this->_cost = $this->_makeInt($config->cost);
        $this->_SOW = $config->SOW;
        
        if (isset($config->facers))
            $this->_loadTrimers();
    }
    
    /**
     * Convert INI file internal variables into real values
     *
     * @param Zend_Config_Ini INI file with configuration
     * @return void
     **/
    protected function _parse(Zend_Config_Ini $config) {
        foreach ($config as $key=>$value) {
            $config->$key = $this->_translate($value);
            if ($value instanceof Zend_Config_Ini)
                $this->_parse($value);
        }
        return $config;
    }
    
    /**
     * Translate one line/string into another
     *
     * @param string Line of text
     * @return string
     **/
    protected function _translate($line) {
        $matches = array();
        if (!preg_match_all('/(\+|\#|\!)\{([\w\d]+)\}/', $line, $matches))
            return $line;
        foreach ($matches[0] as $id=>$match) {
            $metric = $this->_wbs->owner->metrics->get($matches[2][$id]);
            switch ($matches[1][$id]) {
                case '+':
                    $replacer = $metric->getTarget() - $metric->getValue();
                    break;
                case '!':
                    $replacer = $metric->getValue();
                    break;
                case '#':
                    $replacer = $metric->getTarget();
                    break;
            }
            $line = str_replace($match, $replacer, $line);
        }
        return $line;
    }
    
    /**
     * Make it integer
     *
     * @param string Line of text, which COULD be int or a formula
     * @return integer
     **/
    protected function _makeInt($line) {
        if (is_numeric($line))
            return (integer)$line;
        eval('$value = ' . $line . ';');
        return $value;
    }
    
}
