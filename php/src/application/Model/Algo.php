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
 * @version $Id: Colors.php 729 2010-02-22 12:06:48Z yegor256@yahoo.com $
 *
 */

/**
 * Factory of algorithms
 *
 * Individual algorithms are stored in Model/Algo/*
 *
 * @package Model
 */
class Model_Algo
{

    /**
     * List of options
     *
     * @var string[]
     */
    protected $_options = array();

    /**
     * Construct it
     *
     * @param array List of options, for {@link $this->_options} to set
     * @return void
     */
    private function __construct(array $options = array()) 
    {
        $this->setOptions($options);
    }

    /**
     * Create new algorythm
     *
     * @param string Class name to instantiate
     * @param array List of options, for {@link $this->_options} to set
     * @return Model_Algo
     */
    public static function factory($name, array $options = array()) 
    {
        $className = 'Model_Algo_' . ucfirst($name);
        return new $className($options);
    }
    
    /**
     * Set options
     *
     * @param array List of options, for {@link $this->_options} to set
     * @return $this
     * @throws Model_Algo_InvalidOptionException
     */
    public function setOptions(array $options) 
    {
        foreach ($options as $option=>$value) {
            if (!array_key_exists($option, $this->_options)) {
                FaZend_Exception::raise(
                    'Model_Algo_InvalidOptionException',
                    "Option '{$option}' is not recognized in " . get_class($this)
                );
            }
            $this->_options[$option] = $value;
        }
        return $this;
    }

}
