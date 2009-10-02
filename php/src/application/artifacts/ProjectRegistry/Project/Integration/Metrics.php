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
 * Project metrics collector
 *
 * @package Artifacts
 */
class theMetrics extends Model_Artifact_Bag {

    /**
     * Get a metric class
     *
     * @param string Name of the metric, e.g. defectsFound
     * @return theMtcAbstract
     **/
    public function get($metric) {
        validate()->regex($metric, '/^(\w+\/?)+$/', "Metric name should be formatted: name/name/name/... etc.");
        
        $exp = explode('/', $metric);
        $metricName = 'Mtc' . ucfirst(array_pop($exp));
        $metricClass = 'the' . $metricName;
        
        // build the absolute path of PHP metric file
        foreach ($exp as &$dir)
            $dir = ucfirst($dir);
        
        // include this particular metric file
        $file = dirname(__FILE__) . '/Metrics/' . implode('/', $exp) . '/' . $metricName . '.php';
        if (!file_exists($file))
            FaZend_Exception::raise('MetricsNotFound', "Metric '{$metric}' not found");

        // attach this metric to the holder
        $this->_attachItem($metric, new $metricClass(), 'setMetrics');
        
        return $this[$metric];
    }
    
    /**
     * Get an array of ALL metrics in the project
     *
     * @param string Prefix to apply
     * @return theMtcAbstract[]
     **/
    public function getAll($prefix = '') {
        $path = dirname(__FILE__) . '/Metrics/' . $prefix;
        $metrics = array();
        foreach (glob($path . '/*') as $file) {
            $metric = pathinfo($file, PATHINFO_FILENAME);
            if (is_dir($file))
                $metrics += $this->getAll(lcfirst($metric) . '/');
            elseif ($prefix) {
                // PHP 5.3 only: lcfirst()
                $metricName = $prefix . lcfirst(substr($metric, 3));
                $metrics[$metricName] = $this->get($metricName);
            }
        }
        
        if (!$prefix)
            return new ArrayIterator($metrics);
        else
            return $metrics;
    }
            
}
