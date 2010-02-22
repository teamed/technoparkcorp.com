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
     * @throws Opportunity_RenderingException
     */
    public function getLatex()
    {
        $texry = new Model_Texry('a4pdf.tex');
        try {
            $texry->assign(
                'document', 
                $this->sheets->getLatex()
            );
        } catch (SheetsCollection_RenderingException $e) {
            FaZend_Exception::raise(
                'Opportunity_RenderingException',
                "Failed to render: {$e->getMessage()}"
            );
        }
        $tex = $texry->render();
        
        return $tex;
    }
    
    /**
     * Send document by email
     *
     * @param string Email to use
     * @return void
     */
    public function sendByEmail($email) 
    {
        $tex = $this->getLatex();
        FaZend_Email::create()
            ->set('body', "PDF offer for '{$this->id}'attached")
            ->set('toEmail', $email)
            ->attach(new Zend_Mime_Part($tex))
            ->send();
        
        logg(
            'LaTeX source sent to email: %s (%d bytes)', 
            $email,
            strlen($tex)
        );
    }
    
}
