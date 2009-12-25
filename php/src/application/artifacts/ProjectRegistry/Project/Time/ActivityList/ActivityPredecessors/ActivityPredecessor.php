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
 * List of activity predecessor
 *
 * @package Artifacts
 */
class theActivityPredecessor
{

    const FINISH_TO_START = 'FS';
    const FINISH_TO_FINISH = 'FF';
    const START_TO_START = 'SS';
    const START_TO_FINISH = 'SF';
    
    /**
     * Predecessor
     *
     * @var theActivity
     */
    protected $_activity;
    
    /**
     * Type of link
     *
     * @var string
     */
    protected $_type;
    
    /**
     * Days!
     *
     * @var integer
     */
    protected $_lag;

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        $str = (string)$this->_activity->name;
        if (($this->_type !== self::FINISH_TO_START) || $this->_lag) 
            $str .= ':' . $this->_type . $this->_lag . 'hrs';
        return $str;
    }
    
    /**
     * Create this class
     *
     * @param theActivity Predecessor
     * @param string Type
     * @param integer Lag in hours
     * @return $this
     */
    public static function factory(theActivity $activity, $type, $lag)
    {
        $predecessor = new self();
        $predecessor->_activity = $activity;
        $predecessor->_type = $type;
        $predecessor->_lag = $lag;
        return $predecessor;
    }

    /**
     * Calculate start of this activity
     *
     * @param theActivity We shall use it as a basis, to calculate ITS start
     * @return Zend_Date
     **/
    public function calculateStart(theActivity $activity)
    {
        switch ($this->_type) {
            case self::FINISH_TO_START:
                $start = $this->_activity->finish;
                break;

            case self::FINISH_TO_FINISH:
                $start = $this->_activity->finish;
                $start->sub($activity->duration, Zend_Date::DAY);
                break;

            case self::START_TO_START:
                $start = $this->_activity->start;
                break;

            case self::START_TO_FINISH:
                $start = $this->_activity->start;
                $start->sub($activity->duration, Zend_Date::DAY);
                break;
                
            default:
                FaZend_Exception::raise(
                    'ActivityPredecessorInvalidType',
                    "Invalid type of predecessor: '{$this->_type}'"
                    );
        }
        
        $start->add($this->_lag, Zend_Date::DAY);
        
        return $start;
    }

}
