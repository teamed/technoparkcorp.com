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
 * Helper for passive classes loading
 *
 * @package Artifacts
 */
class Model_Artifact_Passive_Loader
{

    /**
     * Class to load
     *
     * @var Model_Artifact_Passive
     **/
    protected $_class;
    
    /**
     * List of vars to attach
     *
     * @var array
     **/
    protected $_toAttach = array();

    /**
     * Creates new loader
     *
     * @param Model_Artifact_Passive Class to load
     * @return Model_Artifact_Passive_Loader
     **/
    public static function factory(Model_Artifact_Passive $class) 
    {
        return new self($class);
    }
    
    /**
     * Construct the class
     *
     * @param Model_Artifact_Passive Class to load
     * @return void
     */
    public function __construct(Model_Artifact_Passive $class)
    {
        $this->_class = $class;
    }

    /**
     * undocumented function
     *
     * @return $this
     **/
    public function attach($var, Model_Artifact_Interface $kid, $property = null) 
    {
        $this->_toAttach[$var] = array(
            'artifact' => $kid, 
            'property' => $property
            );
        return $this;
    }

    /**
     * The class is loaded?
     *
     * @return boolean
     **/
    public function isLoaded() 
    {
        foreach ($this->_toAttach as $name=>$attach) {
            if (!isset($this->_class->{$name}))
                return false;
        }
        return true;
    }
    
    /**
     * Reload everything
     *
     * @return void
     **/
    public function reload() 
    {
        $className = get_class($this->_class);
        $mediator =  "{$className}_Loader_" . md5($className);
        
        if (!class_exists($mediator, false)) {
            eval(
                "class {$mediator} extends {$className}
                {
                    public static function load_{$mediator}(\$class)
                    {
                        \$args = func_get_args();
                        array_shift(\$args);
                        call_user_func_array(array(\$class, '_attach'), \$args);
                    }
                }"
            );
        }
            
        foreach ($this->_toAttach as $name=>$attach) {
            eval(
                "{$mediator}::load_{$mediator}(\$this->_class, 
                '{$name}', 
                \$attach['artifact'], 
                \$attach['property']);"
            );
        }
        
        // make this artifact dirty, to save changes from activities
        $this->_class->ps()->setDirty();
    }

}
