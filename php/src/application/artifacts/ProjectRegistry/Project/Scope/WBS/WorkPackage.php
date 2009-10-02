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
    public $wbs;
    
    /**
     * Config of the WP
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * Create new work package from INI file
     *
     * @param string Code of the work package
     * @param Zend_Config_Ini INI file with configuration
     * @return void
     **/
    public function __construct($code, Zend_Config_Ini $config) {
        $this->_code = $code;
        $this->_config = $config;
    }
    
    /**
     * Getter
     *
     * @return mixed
     **/
    public function __get($name) {
        switch ($name) {
            case 'cost':
                return $this->_makeInt($this->_translate($this->_config->cost));
            case 'sow':
                return $this->_translate($this->_config->sow);
            case 'code':
                return $this->_code;
        }
    }
    
    /**
     * Get list of all activities
     *
     * @return theActivity[]
     */
    public function getActivities() {
        $activities = new theActivities();
        
        $activities[] = new theActivity($this);
        
        return $activities;
    }
    
    /**
     * Translate one line/string into another
     *
     * @param string Line of text
     * @return string
     **/
    protected function _translate($line) {
        $matches = array();
        if (!preg_match_all('/(\+|\#|\!)\{([\w\d\/]+)\}/', $line, $matches))
            return $line;
        foreach ($matches[0] as $id=>$match) {
            $metric = $this->wbs->project->metrics->get($matches[2][$id]);
            switch ($matches[1][$id]) {
                case '+':
                    $replacer = $metric->delta;
                    break;
                case '!':
                    $replacer = $metric->value;
                    break;
                case '#':
                    $replacer = $metric->target;
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
