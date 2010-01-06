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
class theMetrics extends Model_Artifact_Bag implements Model_Artifact_Passive 
{

    const SEPARATOR = '/';
    
    /**
     * Autoloader of metrics
     *
     * @var Zend_Loader_Autoloader
     **/
    protected static $_autoloader = null;

    /**
     * Initialize autoloader of metrics, to be called from bootstrap
     *
     * @return void
     **/
    public static function initAutoloader() 
    {
        // enable this directory for class loading
        self::$_autoloader = Zend_Loader_Autoloader::getInstance();
        self::$_autoloader->registerNamespace('Metric_');
        set_include_path(get_include_path() . PATH_SEPARATOR . 
            dirname(__FILE__) . '/metrics-library');
    }
    
    /**
     * Is it reloaded?
     *
     * @return boolean
     **/
    public function isLoaded() 
    {
        return count($this) > 1;
    }
    
    /**
     * Reload all metrics
     *
     * @return void
     **/
    public function reload() 
    {
        // remove all items from the array
        $this->ps()->cleanArray();

        // here we have all project metrics
        $path = dirname(__FILE__) . '/metrics-library';        

        // by means of this REGEX we extract the relative name of the file
        $regexp = '/^' . preg_quote($path, '/') . '((?:\/\w+)*?\/\w+)\.php$/';        

        // go through the list of all files in "metrics-library"
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
            // skip abstract classes
            if ($file->getFileName() == 'Abstract.php')
                continue;
            
            // skip all other files, which are not related to metrics
            if (!preg_match($regexp, $file->getPathName(), $matches))
                continue;
                
            // we explicitly ask the class to load this metric
            // @see offsetGet()
            $this[$this->_fileToName(trim($matches[1], '/'))];
        }
    }

    /**
     * Find metric by ID
     *
     * @param string ID of the metric to find
     * @return Metric_Abstract
     **/
    public function findById($id) 
    {
        return $this[Model_Pages_Encoder::decode($id)];
    }
    
    /**
     * Get metric even if it doesn't exist in array
     *
     * Here we catch any attempts to get a metric out of collection. We
     * either return an existing metric, or we're trying to load it.
     *
     * @param string Full name of metric, e.g. 'requirements/useCases/total'
     * @return Metric_Abstract
     * @throws MetricNotFound
     **/
    public function offsetGet($name) 
    {
        // if the metric is in array - we just return it
        if (parent::offsetExists($name))
            return parent::offsetGet($name);
        // otherwise we find it and attach
        return $this->_findMetric($name);
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
    protected function _findMetric($name) 
    {
        // maybe we can load it straight away?
        if ($this->_attachMetric($name))
            return parent::offsetGet($name);

        // break down the name of the metric onto parts
        $parts = explode(self::SEPARATOR, $name);
        
        // go from end to start
        $parentName = '';
        for ($i = count($parts)-1; $i > 0; $i--) {
            $parentName = implode(self::SEPARATOR, array_slice($parts, 0, $i));
            
            if ($this->_attachMetric($parentName)) {
                $parent = parent::offsetGet($parentName);
                break;
            }
        }

        // if the requirement is not found up to the top-level element
        if (!isset($parent) || !$parent) {
            $exists = $this->getArrayCopy();
            FaZend_Exception::raise('MetricNotFound', 
                "Metric '{$name}' not found for parent '{$parentName}', " . count($exists) . ' total in collection: ' . 
                    implode(', ', array_keys($exists)));
        }

        $pattern = implode(self::SEPARATOR, array_slice($parts, $i));
        if (!$parent->isMatched($pattern)) {
            FaZend_Exception::raise('MetricDoesntMatch',
                "Metric '{$name}' doesn't match pattern '{$pattern}' in metric '{$parent->name}'");
        }
            
        $this->_attachMetric($name, $parent->cloneByPattern($pattern));
        return parent::offsetGet($name);
    }

    /**
     * Load and attach one metric to the collection
     *
     * @param string Name of the metric, like 'requirements/total'
     * @param null|Metric_Abstract Metric class, if we already instantiated it
     * @return boolean Attached or not?
     **/
    protected function _attachMetric($name, Metric_Abstract $metric = null) 
    {
        // don't add it again, if it exists
        if (isset($this[$name]))
            return true;
            
        if (is_null($metric)) {
            $className = $this->_nameToClass($name);

            if (is_null(self::$_autoloader)) {
                FaZend_Exception::raise('MetricAutoloaderNotInitialized',
                    "You should call ::initAutoloader() in your bootstrap");
            }

            if (!@self::$_autoloader->autoload($className, false))
                return false;
            $metric = new $className;
        }

        $metric->setName($name);
        
        // attach and reload it explicitly
        try {
            $this->_attachItem($name, $metric, 'setMetrics');
            $this[$name]->reload();
        } catch (Exception $e) {
            // failure? remove it from collection!
            unset($this[$name]);
            logg("Metric [{$name}] failed to reload, won't be attached; " .
                get_class($e) . ': ' . $e->getMessage());
            return false;
        }
        logg('New metric attached and reloaded: ' . $name);
        return true;
    }
    
    /**
     * Convert metric file name to metric name
     *
     * @param string Class name like 'Metric/Code/Sloc.php'
     * @return string Metric name like 'code/sloc'
     */
    protected function _fileToName($fileName) 
    {
        $parts = explode('/', $fileName);
        validate()->true(array_shift($parts) == 'Metric', 
            "Metric file name shall start with Metric: '{$fileName}'");
        foreach ($parts as &$sector)
            $sector = lcfirst($sector);
        return implode(self::SEPARATOR, $parts);
    }
    
    /**
     * Convert metric name to classs name
     *
     * @param string Metric name like 'code/sloc'
     * @return string Class name like 'Metric_Code_Sloc'
     */
    protected function _nameToClass($name) 
    {
        $parts = explode(self::SEPARATOR, $name);
        foreach ($parts as &$sector)
            $sector = ucfirst($sector);
        return 'Metric_' . implode('_', $parts);
    }
    
}
