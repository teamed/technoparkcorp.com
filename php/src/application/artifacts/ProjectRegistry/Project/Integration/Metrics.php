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

    /**
     * Is it reloaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return (bool)count($this);
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
        $autoloader->registerNamespace('Metrics_');
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

            $this->_attachItem($metricName, new $className(), 'setMetrics');
            $new++;
        }
        
        // remove obsolete metrics, which are absent in files
        $deleted = 0;
        foreach ($this as $key=>$metric) {
            if (!in_array($key, $added)) {
                unset($this[$key]);
                $deleted++;
            }
        }
                
        logg('Reloaded ' . count($this) . ' metrics in ' . $this->ps()->parent->name 
            . ', ' . $new . ' added, ' . $deleted . ' deleted');
    }
    
    /**
     * Convert metric class name to metric name
     *
     * @param string Class name like 'Metric_Code_Sloc'
     * @return string Metric name like 'code/sloc'
     */
    protected function _classToName($className) {
        $exp = array_filter(explode('/', $path));
        
        validate()->true(array_shift($exp) == 'Metric');
        
        foreach ($exp as &$sector)
            // PHP 5.3 only: lcfirst()
            $sector = lcfirst($sector);
        return implode('/', $exp);
    }
            
}
