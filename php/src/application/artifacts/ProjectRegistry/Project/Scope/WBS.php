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
class theWBS extends Model_Artifact_Bag {
    
    /**
     * Returns a list of activities to be done now, according to current WBS
     *
     * @return theActivity[]
     **/
    public function getActivities() {
        // returns them
    }
    
    /**
     * Load work packages into the WBS, before iteration
     *
     * @return mixed
     **/
    public function count() {
        // already loaded?
        if (parent::count() == 0) {
            foreach ($this->_getListOfIniFiles() as $code=>$file)
                $this[$code] = new theWorkPackage($this, $code, new Zend_Config_Ini($file, 'wp', array(
                    'allowModifications'=>true)));
        }
        
        return parent::count();
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
