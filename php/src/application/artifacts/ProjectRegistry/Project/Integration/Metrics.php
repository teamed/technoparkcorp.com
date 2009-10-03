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
        $path = dirname(__FILE__) . '/Metrics';        
        $regexp = '/^' . preg_quote($path, '/') . '(?:(\/\w+)*?)\/(Mtc([^(Abstract)]\w+))\.php$/';        
        $matches = array();
        $added = array();
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
            if (!preg_match($regexp, $file->getPathName(), $matches))
                continue;
                
            $metricName = $this->_pathToName($matches[1] . '/' . $matches[3]);
            $added[] = $metricName;
            
            // don't add it again, if it exists
            if (isset($this[$metricName]))
                continue;

            $newClassName = 'standaloneMetric_' . str_replace('/', '_', $metricName);
            if (!class_exists($newClassName)) {
                $php = str_replace('<?php', '', php_strip_whitespace($file->getPathName()));
                $php = preg_replace('/(class\s)the' . $matches[2] . '(\sextends\stheMtcAbstract[\s\{])/', 
                    '${1}' . $newClassName . '${2}', $php);
                eval($php);
            }
            
            $this->_attachItem($metricName, new $newClassName(), 'setMetrics');
        }
        
        // remove obsolete metrics, which are absent in files
        foreach ($this as $key=>$metric)
            if (!in_array($key, $added))
                unset($this[$key]);
    }
    
    /**
     * Convert metric path to name
     *
     * @param string
     * @return string
     */
    protected function _pathToName($path) {
        $exp = array_filter(explode('/', $path));
        foreach ($exp as &$sector)
            // PHP 5.3 only: lcfirst()
            $sector = lcfirst($sector);
        return implode('/', $exp);
    }
            
}
