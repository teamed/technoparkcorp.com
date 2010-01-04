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
 * Project work breakdown structure, collection of work packages
 *
 * @package Artifacts
 */
class theWbs extends Model_Artifact_Bag implements Model_Artifact_Passive
{
    
    /**
     * Load work packages into the WBS, before iteration
     *
     * @return void
     **/
    public function reload() 
    {
        // remove all items from the array
        $this->ps()->cleanArray();
        
        // add new from metrics
        foreach ($this->ps()->parent->metrics as $metric)
            $this->_findWorkPackage($metric->name);
    }
    
    /**
     * WBS is loaded?
     *
     * @return boolean
     **/
    public function isLoaded() 
    {
        return (bool)count($this);
    }
    
    /**
     * Get WP even if it doesn't exist in array
     *
     * @return theWorkPackage
     **/
    public function offsetGet($name) 
    {
        $wp = $this->_findWorkPackage($name);
        if (is_null($wp)) {
            FaZend_Exception::raise('WorkPackageAbsent', 
                "Metric '{$name}' does not have a work package in " . get_class($metric) . "::getWorkPackage()");
        }
        return $wp;
    }

    /**
     * Summarize work packages and return their cummulative cost
     *
     * @param array|string Name or list of names - regular expressions
     * @return Model_Cost
     **/
    public function sum($regexs = '') 
    {
        if (!is_array($regexs))
            $regexs = array($regexs);
            
        $sum = new Model_Cost();
        foreach ($regexs as $regex) {
            foreach ($this->ps()->parent->metrics as $metric) {
                if (!preg_match('/' . $regex . '/', $metric->name))
                    continue;
                $wp = $this->_findWorkPackage($metric->name);
                if ($wp)
                    $sum->add($wp->cost);
                
            }
        }
        return $sum;
    }
    
    /**
     * Get list of workpackages with given prefix and all aggregators below them
     *
     * @return theWorkPackage[]
     **/
    public function getWorkPackagesByPrefix($prefix = '') 
    {
        $list = new ArrayIterator();
        foreach ($this as $wp) {
            
            $code = $wp->code;
            // wrong way
            if (!preg_match('/^' . preg_quote($prefix . ($prefix ? theMetrics::SEPARATOR : false), '/') . 
                '([\w\d]+)(?:' . preg_quote(theMetrics::SEPARATOR, '/') . '(.*))?$/', $code, $matches))
                continue;
                
            // bug($matches);
            // we are right here, not below, not in sub-packages!
            if (empty($matches[2])) {
                $list[$matches[1] . '*'] = $wp;
                continue;
            }
                
            if (!isset($list[$matches[1]]))
                $list[$matches[1]] = new theWpAggregator($prefix . ($prefix ? theMetrics::SEPARATOR : false) . $matches[1], null, null);
            $list[$matches[1]]->addWorkPackage($wp);
        }
        
        return $list;
    }
    
    /**
     * Find work package or make an aggregate
     *
     * @param string Code of WP to be found or built
     * @return theWorkPackage
     * @throws WorkPackageCantBeMade
     **/
    public function findOrMakeWp($code) 
    {
        try {
            $wp = $this->_findWorkPackage($code);
            if (!$wp)
                FaZend_Exception::raise('WorkPackageNotFound', 
                    "Metric {$code} doesn't have a work package");
            return $wp;
        } catch (WorkPackageNotFound $e) {
            // just go forward
        }

        if (strpos($code, theMetrics::SEPARATOR) === false) {
            $parent = '';
            $kid = $code;
        } else {
            $parent = substr($code, 0, strrpos($code, theMetrics::SEPARATOR));
            $kid = substr(strrchr($code, theMetrics::SEPARATOR), 1);
        }
        $wps = $this->getWorkPackagesByPrefix($parent);
        
        if (!isset($wps[$kid]))
            FaZend_Exception::raise('WorkPackageCantBeMade', 
                "WP '{$kid}' not found in parent '{$parent}', while " . count($wps) . ' WPs found there');
                
        return $wps[$kid];
                
    }
    
    /**
     * Get WP or return NULL
     *
     * @param string Name of the work package
     * @return theWorkPackage
     * @throws WorkPackageNotFound
     **/
    protected function _findWorkPackage($code) 
    {
        $wps = $this->getArrayCopy();
        if (isset($wps[$code])) {
            return $wps[$code];
        }

        try {
            $metric = $this->ps()->parent->metrics[$code];
        } catch (MetricNotFound $e) {
            FaZend_Exception::raise('WorkPackageNotFound', $e->getMessage());
        } catch (MetricDoesntMatch $e) {
            FaZend_Exception::raise('WorkPackageNotFound', $e->getMessage());
        }
        
        $wp = $metric->getWorkPackage();
        if (!$wp)
            return false;
            
        $this->_attachItem($wp->code, $wp, 'setWbs');
        return $wp;
    }

}
