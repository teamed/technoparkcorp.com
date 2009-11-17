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
class theMetrics extends Model_Artifact_Bag implements Model_Artifact_Passive {

    const SEPARATOR = '/';

    /**
     * Is it reloaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return count($this) > 1;
    }
    
    /**
     * Reload all metrics
     *
     * @return void
     **/
    public function reload() {
        // here we have all project metrics
        $path = dirname(__FILE__) . '/metrics-library';        

        // enable this directory for class loading
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Metric_');
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);

        $regexp = '/^' . preg_quote($path, '/') . '((?:\/\w+)*?)\/(\w+)\.php$/';        
        $added = array();
        $new = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
            // skip abstract classes
            if ($file->getFileName() == 'Abstract.php')
                continue;
            
            // skip all other files, which are not related to metrics
            if (!preg_match($regexp, $file->getPathName(), $matches))
                continue;
                
            $className = str_replace('/', '_', trim($matches[1], '/')) . '_' . $matches[2];
            $metricName = $this->_classToName($className);
            $added[] = $metricName;
            
            // don't add it again, if it exists
            if (isset($this[$metricName]))
                continue;

            $this->_attachMetric($metricName, $className);
            $new++;
            // logg('Reloaded ' . $metricName);
        }
        bug(array_keys($this->getArrayCopy()));
        
        // logg('Reloaded ' . count($this) . ' metrics in ' . $this->ps()->parent->name);
    }

    /**
     * Get metric even if it doesn't exist in array
     *
     * @param string Full name of metric, e.g. 'requirements/useCases/total'
     * @return Metric_Abstract
     * @throws MetricNotFound
     **/
    public function offsetGet($name) {
        $metrics = $this->getArrayCopy();
        if (isset($metrics[$name])) {
            return $metrics[$name];
        }

        // top level metric can't be used in patterning
        if (strpos($name, self::SEPARATOR) === false)
            FaZend_Exception::raise('MetricNotFound', "Metric '{$name}' not found in collection");
        
        return $this->_findMetric($name);
    }
    
    /**
     * Find metric by ID
     *
     * @param string ID of the metric to find
     * @return Metric_Abstract
     **/
    public function findById($id) {
        return $this[Model_Pages_Encoder::decode($id)];
    }
    
    /**
     * Attach one metric to the collection
     *
     * @param string Name of the metric, like 'requirements/total'
     * @param string|Metric_Abstract Name of the class, like 'Metric_Requirements_Total'
     * @return Metric_Abstract
     **/
    protected function _attachMetric($name, $class) {
        if (!($class instanceof Metric_Abstract))
            $class = new $class();
            
        $class->setName($name);
        $this->_attachItem($name, $class, 'setMetrics');

        return $class;       
    }
    
    /**
     * Convert metric class name to metric name
     *
     * @param string Class name like 'Metric_Code_Sloc'
     * @return string Metric name like 'code/sloc'
     */
    protected function _classToName($className) {
        $exp = array_filter(explode('_', $className));
        
        validate()->true(array_shift($exp) == 'Metric', "Metric class name shall start with Metric: '$className'");
        
        foreach ($exp as &$sector)
            // PHP 5.3 only: lcfirst()
            $sector = lcfirst($sector);
        return implode(self::SEPARATOR, $exp);
    }
            
    /**
     * Find metric and create it, if possible
     *
     * Method is called from offsetGet() when it's not possible to find a metric
     * in existing array. We have to create a metric dynamically. If it's possible.
     *
     * @see offsetGet()
     * @return Metric_Abstract
     * @throws MetricNotFound
     **/
    protected function _findMetric($name) {
        $exp = explode(self::SEPARATOR, $name);
        
        // go from end to start
        for ($i=count($exp)-1; $i>0; $i--) {
            $parent = implode(self::SEPARATOR, array_slice($exp, 0, $i));
            
            if (isset($this[$parent])) {
                $metric = $this[$parent];
                break;
            }
        }

        // if the requirement is not found up to the top-level element
        if (!isset($metric)) {
            $arr = $this->getArrayCopy();
            FaZend_Exception::raise('MetricNotFound', 
                "Metric '{$name}' not found for parent '{$parent}', " . count($this) . ' total in collection: ' . 
                    implode(', ', array_keys($arr)));
        }

        $pattern = implode(self::SEPARATOR, array_slice($exp, $i));
        if (!$metric->isMatched($pattern))
            FaZend_Exception::raise('MetricDoesntMatch',
                "Metric '{$name}' doesn't match you pattern: '{$pattern}'");
            
        return $this->_attachMetric($name, $metric->cloneByPattern($pattern));
    }

}
