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
class theWbs extends Model_Artifact_Bag implements Model_Artifact_Passive {
    
    /**
     * Returns a list of activities to be done now, according to current WBS
     *
     * @return theActivities
     **/
    public function getActivities() {
        $activities = new theActivities();
        $splitter = new theActivitySplitter($this, $activities);
        foreach ($this as $wp) {
            $splitter->dispatch($wp);
        }
        return $activities;
    }
    
    /**
     * Load work packages into the WBS, before iteration
     *
     * @return void
     **/
    public function reload() {
        foreach ($this->_getListOfIniFiles() as $code=>$file)
            $this->_attachItem($code, new theWorkPackage($code, 
                new Zend_Config_Ini($file, 'wp', array('allowModifications'=>true))), 'setWbs');
    }
    
    /**
     * WBS is loaded?
     *
     * @return boolean
     **/
    public function isLoaded() {
        return (bool)count($this);
    }
    
    /**
     * Translate one line/string into another
     *
     * @param string Line of text
     * @param boolean Expected value is numeric?
     * @return string
     **/
    public function translateIni($line, $numeric = false) {
        $matches = array();
        if (!preg_match_all('/(\+|\#|\!)\{([\w\d\/]+)\}/', $line, $matches))
            return $line;
        foreach ($matches[0] as $id=>$match) {
            $metric = $this->ps()->parent->metrics[$matches[2][$id]];
            switch ($matches[1][$id]) {
                case '+':
                    $replacer = $metric->delta;
                    break;
                case '!':
                    $replacer = $metric->value;
                    break;
                case '#':
                    $replacer = $metric->target;
                    break;
            }
            $line = str_replace($match, $replacer, $line);
        }
        
        // try to convert it
        if ($numeric && !is_numeric($line))
            eval('$line = ' . $line . ';');
        
        return $line;
    }
    
    /**
     * Get list of all packages
     *
     * Returns an associative array where key is a code of the package
     * and the value is an absolute path of the INI file with the config
     *
     * @return string[]
     **/
    protected function _getListOfIniFiles($path = null, $prefix = '') {

        // all INI files are here, in /packages directory
        if (is_null($path))
            $path = realpath(dirname(__FILE__) . '/WBS/packages');
            
        $files = array();
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file))
                $files += $this->_getListOfIniFiles($file, $prefix . pathinfo($file, PATHINFO_FILENAME) . '.');
            else
                $files[$prefix . pathinfo($file, PATHINFO_FILENAME)] = $file;
        }
        return $files;
        
        
    }
    
}
