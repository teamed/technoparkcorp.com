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
 * @version $Id: Supplier.php 637 2010-02-09 09:49:56Z yegor256@yahoo.com $
 *
 */

/**
 * One sales opportunity
 *
 * @package Artifacts
 */
class theOpportunity
{

    /**
     * Id
     *
     * @var string
     */
    protected $_id;
    
    /**
     * Collection of sales sheets
     *
     * @var theSheetsCollection[]
     */
    protected $_sheets;
    
    /**
     * Construct the class
     *
     * @param string Id
     * @return void
     */
    public function __construct($id) 
    {
        $this->_id = $id;
        $this->_sheets = new theSheetsCollection();
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return mixed
     * @throws Opportunity_PropertyOrMethodNotFound
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
            'Opportunity_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this)
        );
    }
    
    /**
     * Get opportunity document in LaTeX
     *
     * @return string
     */
    public function getLatex()
    {
        return $this->sheets->getLatex();
    }
    
    /**
     * Send document by email
     *
     * @param string Email to use
     * @return void
     */
    public function sendByEmail($email) 
    {
        // ...
        logg('PDF request sent to TeXry.com, email: %s', $email);
    }
    
}
