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
 * Project schedule, collection of activities
 *
 * @package Artifacts
 */
class theSchedule extends Model_Artifact_Bag implements Model_Artifact_Passive 
{

    /**
     * It is loaded already?
     *
     * @return boolean
     */
    public function isLoaded() 
    {
        return isset($this->activities);
    }
    
    /**
     * Initialize the list
     *
     * @return void
     */
    public function reload() 
    {
        $activityList = $this->ps()->parent->activityList;
        
        if (!isset($activityList->activities)) {
            $activityList->reload();
            if (!isset($activityList->activities)) {
                FaZend_Exception::raise(
                    'ActivitiesNotFound',
                    'Activities not found in ActivityList after reloading, why?'
                    );
            }
        }
        
        $this->_attach('activities', clone $activityList->activities);
        
        // todo: implement it later
        // $this->activities->getSlice()->resolveMilestones();
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
            
        return parent::__get($name);
    }

    /**
     * Get finish date of schedule
     *
     * @return Zend_Date
     **/
    protected function _getFinish() 
    {
        $finish = new Zend_Date();
        // find the latest finish
        foreach ($this->activities as $activity) {
            if ($finish->compare($activity->finish) == -1)
                $finish = $activity->finish;
        }
        return $finish;
    }
    
}
