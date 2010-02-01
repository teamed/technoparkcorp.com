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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * Loader of artifacts
 *
 * @see http://framework.zend.com/manual/en/zend.loader.autoloader.html
 */
class Model_Loader_Artifacts implements Zend_Loader_Autoloader_Interface
{

    /**
     * Mapping array
     *
     * @var array
     */
    protected $_mapping;

    /**
     * Load class
     *
     * Class name should start with 'the'
     *
     * @param string Class name that should be resolved
     * @return Model_Artifact
     * @throws Model_Loader_Artifacts_NotFound
     */
    public function autoload($class)
    {
        if (class_exists($class, false))
            return;

        if (substr($class, 0, 3) !== 'the') {
            FaZend_Exception::raise(
                'Model_Loader_Artifacts_InvalidName', 
                "Class $class has invalid name for this loader"
            );
        }

        if (!$this->_exists($class)) {
            FaZend_Exception::raise(
                'Model_Loader_Artifacts_NotFound', 
                "Class $class not found"
            );
        }

        require_once($this->_classFile($class));
    }

    /**
     * Build an array of class=>file associations
     *
     * @param string Class name
     * @return array
     */
    protected function _getMapping($class = null)
    {
        if (!isset($this->_mapping)) {
            $this->_mapping = $this->_grab(APPLICATION_PATH . '/artifacts');
        }

        if (!is_null($class))
            return $this->_mapping[$class];
        else
            return $this->_mapping;
    }

    /**
     * Build an array of class=>file associations
     *
     * @param string Name of the class
     * @return array
     */
    protected function _exists($class)
    {
        return array_key_exists($class, $this->_getMapping());
    }

    /**
     * Build an array of class=>file associations
     *
     * @param string Class name
     * @return array
     */
    protected function _classFile($class)
    {
        return $this->_getMapping($class);
    }

    /**
     * Recursively grab artifact files
     *
     * @param string Path
     * @return array
     */
    protected function _grab($path)
    {
        $files = array();
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file))
                $files += $this->_grab($file);
            else
                $files['the' . pathinfo($file, PATHINFO_FILENAME)] = $file;
        }
        return $files;
    }

}
