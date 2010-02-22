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
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One attachment
 *
 * @package Artifacts
 */
class Model_Artifact_Attachments_Attachment implements Model_Artifact_Stateless
{

    /**
     * Name
     *
     * @var string 
     */
    protected $_name;

    /**
     * Description
     *
     * @var string 
     */
    protected $_description;

    /**
     * Relative file name
     *
     * @var string 
     */
    protected $_fileName = null;

    /**
     * Construct the class
     *
     * @param string Name of the attachment
     * @param string Description of the attachment
     * @param string Absolute(!) file name of the attachment
     * @return void
     */
    public function __construct($name, $description, $file = null)
    {
        $this->_name = $name;
        $this->_description = $description;
        
        if ($file) {
            $this->_fileName = Zend_Date::now('en')->get(YEAR) . '/' . 
            Zend_Date::now('en')->get(MONTH) . '/' . 
            pathinfo($file, PATHINFO_BASENAME);
        
            // move file into special file storage
            if (Model_Artifact_Attachments::getLocation() !== false)
                copy($file, Model_Artifact_Attachments::getLocation() . '/' . $this->_fileName);
        }
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     **/
    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
        
        FaZend_Exception::raise(
            'Attachment_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
}
