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
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Make a shortcut to the document
 *
 * @package helpers
 */
class Helper_Shortcut extends FaZend_View_Helper
{

    /**
     * Name of the document to share
     *
     * @var string
     */
    protected $_document;

    /**
     * Only these users can access the document
     *
     * @var boolean
     */
    protected $_uniqueAccess = false;

    /**
     * List of emails - accessors to this document
     *
     * @var string[]
     */
    protected $_emails = array();
    
    /**
     * Associative array of params
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Builds a link of a shared document
     *
     * @return Helper_Shortcut
     */
    public function shortcut()
    {
        return $this;
    }
    
    /**
     * Convert it to string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->_render();
        } catch (Exception $e) {
            return get_class($e) . ' in ' . get_class($this) . ': ' . $e->getMessage();
        }
    }
    
    /**
     * Make it look like URL
     *
     * @return string
     */
    public function _render()
    {
        return Model_Shortcut::create($document, $this->_emails, $this->_params, $this->_uniqueAccess)
            ->getUrl();
    }
    
    /**
     * Save document into the link
     *
     * @param string Document to share
     * @return $this
     */
    public function setDocument($document)
    {
        $this->_document = $document;
        return $this;
    }

    /**
     * Allow access for this person
     *
     * @param string Email of accessor
     * @return $this
     */
    public function addEmail($email)
    {
        validate()->emailAddress($email, array());
        $this->_emails[] = $email;
        return $this;
    }

    /**
     * Add one parameter
     *
     * @param string Name of parameter
     * @param string Value of it
     * @return $this
     */
    public function addParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * Make sure the document is accessible ONLY to the people specified
     *
     * @return $this
     */
    public function setUniqueAccess()
    {
        $this->_uniqueAccess = true;
        return $this;
    }

}
