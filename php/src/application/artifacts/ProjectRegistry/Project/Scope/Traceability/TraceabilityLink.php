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
 * @version $Id$
 *
 */

/**
 * Traceability link between two individual deliverables
 *
 * @package Artifacts
 */
class theTraceabilityLink
{
    
    const SEPARATOR = ':';
    
    /**
     * FROM what deliverable
     *
     * This is a compressed type+name of the object, for example:
     *  - 'functional:R5.6'
     *  - 'qos:QOS7'
     *  - 'class:Model_User'
     *
     * @var string
     */
    protected $_from;
    
    /**
     * TO what deliverable
     *
     * @var string
     * @see $this->_from
     */
    protected $_to;
    
    /**
     * Deep (from 1 to infinity)
     *
     * @var integer
     */
    protected $_deep = 1;
    
    /**
     * Coverage (from 0 to 1)
     *
     * @var float
     */
    protected $_coverage = 1;
    
    /**
     * Explanation of this link
     *
     * @var string
     */
    protected $_explanation;
    
    /**
     * Get tag for the deliverable
     *
     * @param Deliverables_Abstract Deliverable
     * @return string
     */
    public static function getDeliverableTag(Deliverables_Abstract $deliverable) 
    {
        return $deliverable->type . self::SEPARATOR . $deliverable->name;
    }
    
    /**
     * Create an object
     *
     * @param Deliverables_Abstract Traceability FROM this deliverable
     * @param Deliverables_Abstract Traceability FROM this deliverable
     * @param integer Deep of traceability
     * @param float Coverage
     * @param string Explanation of this link
     * @return void
     **/
    public function __construct(
        Deliverables_Abstract $from,
        Deliverables_Abstract $to,
        $deep = 1, 
        $coverage = 1, 
        $explanation = null
        )
    {
        // initialize the class
        $this->_from = self::getDeliverableTag($from); 
        $this->_to = self::getDeliverableTag($to); 

        validate()->false(
            $this->_from == $this->_to,
            "You can't trace to itself: [{$this->_to}]"
        );
        
        $this->_deep = $deep;
        $this->_coverage = $coverage;
        $this->_explanation = $explanation;
    }
    
    /**
     * Convert it to string
     *
     * @return string
     **/
    public function __toString() 
    {
        return $this->_from . '->' . $this->_to;
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
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
            'Model_Wiki_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );        
    }
    
    /**
     * This is link is TO this given deliverable?
     *
     * @param Deliverables_Abstract|Deliverables_Abstract[]
     * @return boolean
     */
    public function isFrom($deliverables) 
    {
        if (!is_array($deliverables) && !($deliverables instanceof ArrayAccess)) {
            $deliverables = array($deliverables);
        }
        foreach ($deliverables as $d) {
            if (($d->name == $this->fromName) && ($d->type == $this->fromType)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * This is link is TO this given deliverable?
     *
     * @param Deliverables_Abstract|Deliverables_Abstract[]
     * @return boolean
     */
    public function isTo($deliverables) 
    {
        if (!is_array($deliverables) && !($deliverables instanceof ArrayAccess)) {
            $deliverables = array($deliverables);
        }
        foreach ($deliverables as $d) {
            if (($d->name == $this->toName) && ($d->type == $this->toType)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get type of FROM
     *
     * @return string
     */
    protected function _getFromType()
    {
        return substr($this->_from, 0, strpos($this->_from, self::SEPARATOR));
    }
    
    /**
     * Get type of TO
     *
     * @return string
     */
    protected function _getToType()
    {
        return substr($this->_to, 0, strpos($this->_to, self::SEPARATOR));
    }
    
    /**
     * Get name of FROM
     *
     * @return string
     */
    protected function _getFromName()
    {
        return substr($this->_from, strpos($this->_from, self::SEPARATOR)+1);
    }
    
    /**
     * Get name of TO
     *
     * @return string
     */
    protected function _getToName()
    {
        return substr($this->_to, strpos($this->_to, self::SEPARATOR)+1);
    }
    
}
