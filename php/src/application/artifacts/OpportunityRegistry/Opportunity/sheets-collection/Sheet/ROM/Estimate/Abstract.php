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
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id: ROM.php 655 2010-02-11 08:35:28Z yegor256@yahoo.com $
 *
 */

/**
 * Abstract estimate
 *
 * @package Artifacts
 */
abstract class Sheet_ROM_Estimate_Abstract
{
    
    /**
     * Lines from estimator
     *
     * @var string[]
     */
    protected $_lines;
    
    /**
     * Estimator name
     *
     * @var string
     */
    protected $_estimator;
    
    /**
     * Promo text about the estimator
     *
     * @var string
     */
    protected $_promo;
    
    /**
     * Construct the class
     *
     * @param array List of lines from estimator
     * @return void
     */
    private final function __construct(array $lines)
    {
        $this->_lines = $lines;
        $this->_init();
    }
    
    /**
     * Create a class from lines provided
     *
     * @param array List of lines from estimator
     * @return void
     * @throws Sheet_ROM_Estimate_Abstract_MethodNotFoundException
     * @throws Sheet_ROM_Estimate_Abstract_UnknownMethodException
     */
    public static function factory(array $lines) 
    {
        foreach ($lines as $line) {
            $line = trim($line, "\t\r\n ");
            if (preg_match('/^method:\s*([\s\w\-\d]+)\s*$/i', $line, $matches)) {
                $method = preg_replace('/[^a-z]/', '', strtolower($matches[1]));
                switch ($method) {
                    case 'fp':
                    case 'functionpoints':
                        $className = 'FunctionPoints';
                        break;
                    
                    case 'tp':
                    case 'threepoints':
                        $className = 'ThreePoints';
                        break;
                        
                    default:
                        FaZend_Exception::raise(
                            'Sheet_ROM_Estimate_Abstract_UnknownMethodException',
                            "Method '{$method}' is unknown"
                        );
                }
                
                $className = 'Sheet_ROM_Estimate_' . $className;
                return new $className($lines);
            }
        }
        FaZend_Exception::raise(
            'Sheet_ROM_Estimate_Abstract_MethodNotFoundException',
            "Method:* not found in the provided spec"
        );
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     * @throws Sheet_ROM_Estimate_Abstract_PropertyOrMethodNotFound
     */
    public final function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
            
        $var = '_' . $name;
        if (property_exists($this, $var)) {
            return $this->$var;
        }
        
        FaZend_Exception::raise(
            'Sheet_ROM_Estimate_Abstract_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Set name of estimator
     *
     * @param string Estimator
     * @return $this
     */
    public function setEstimator($estimator) 
    {
        $this->_estimator = $estimator;
        return $this;
    }
    
    /**
     * Set promo text for the estimator
     *
     * @param string Promo
     * @return $this
     */
    public function setPromo($promo)
    {
        $this->_promo = $promo;
        return $this;
    }
    
    /**
     * Get total amount of hours, the estimate
     *
     * @return integer
     */
    abstract protected function _getHours();
    
    /**
     * Name of proposal file
     *
     * @return string
     */
    protected function _getProposalFile() 
    {
        return substr(get_class($this), strlen('Sheet_ROM_Estimate_')) . '.tex';
    }
    
    /**
     * To override it...
     *
     * @return void
     */
    protected function _init() 
    {
        // ...
    }
    
}
