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
 * One abstract work package in schedule
 *
 * @package Artifacts
 */
abstract class Sheet_ScheduleEstimate_Package_Abstract
{
    
    /**
     * Injected Sheet_Abstract
     *
     * @var Sheet_Abstract
     */
    protected static $_sheet;
    
    /**
     * Name of the element
     *
     * @var string
     */
    protected $_name;
    
    /**
     * List of dependencies
     *
     * Array of arrays, where every child is an array of two elements. The first
     * element is a name of the bar/milestone, and the second one is a type
     * of dependency, see {@link Sheet_ScheduleEstimate_Chart}
     *
     * @var array[]
     */
    protected $_dependencies;
    
    /**
     * Comment to show
     *
     * @var string
     */
    protected $_comment;
    
    /**
     * Inject Sheet
     *
     * @param Sheet_Abstract Sheet to inject
     * @return void
     * @see Sheet_ScheduleEstimate::_init()
     */
    public static function setSheet(Sheet_Abstract $sheet) 
    {
        self::$_sheet = $sheet;
    }
    
    /**
     * Construct the class
     *
     * @param string Name of the milestone
     * @param string Size of the bar
     * @param array Array of arrays, where every child is an array of two elements. The first
     *              element is a name of the bar/milestone, and the second one is a type
     *              of dependency, see {@link Sheet_ScheduleEstimate_Chart}
     * @param string Name of the milestone
     * @return void
     */
    public function __construct($name, array $dependencies = array(), $comment = null)
    {
        $this->_name = $name;
        $this->_dependencies = $dependencies;
        $this->_comment = $comment;
    }
    
    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     * @throws Opportunity_PropertyOrMethodNotFound
     **/
    public function __get($name) 
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
            'Sheet_ScheduleEstimate_Package_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Create new class using the params provided
     *
     * @param array Associative array of params
     * @param array List of packages to extend
     * @return Sheet_Schedule_Package_Abstract The latest added
     * @see Sheet_Schedule::_init()
     * @throws Sheet_ScheduleEstimate_NotSupportedException
     */
    public static function factory($name, $comment, array $params, array &$packages) 
    {
        if (array_key_exists('depends', $params)) {
            FaZend_Exception::raise(
                'Sheet_ScheduleEstimate_NotSupportedException',
                "Explicit dependency declaration is not supported yet"
            );
        } else {
            if (count($packages) > 0) {
                $dependencies = array(
                    array(
                        $packages[count($packages)-1]->name,
                        Sheet_ScheduleEstimate_Chart::DEP_FS,
                        0
                    )
                );
            } else {
                $dependencies = array();
            }
        }
        
        // is it a milestone?
        if (!array_key_exists('duration', $params)) {
            $packages[] = $m = new Sheet_ScheduleEstimate_Package_Milestone(
                $name,
                $dependencies,
                $comment
            );
            return $m;
        }
        
        // bar
        $packages[] = $bar = new Sheet_ScheduleEstimate_Package_Bar(
            $name,
            $dependencies,
            $comment
        );
        $bar->setDuration($params['duration']);
        // milestone right after it
        $m = self::factory($name . '-milestone', null, array(), $packages);
        $m->setCost($params['cost']);
        return $m;
    }
    
    /**
     * Add package to the chart
     *
     * @param Sheet_ScheduleEstimate_Chart Chart to use
     * @return void
     */
    public function addYourself(Sheet_ScheduleEstimate_Chart $chart)
    {
        foreach ($this->_dependencies as $dep) {
            list($from, $type, $lag) = $dep;
            $chart->addDependency($from, $this->_name, $type, $lag);
        }
    }

}
