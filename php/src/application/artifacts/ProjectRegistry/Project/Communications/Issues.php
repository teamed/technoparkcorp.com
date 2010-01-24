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
 * Project issues
 *
 * @package Artifacts
 */
class theIssues implements Model_Artifact_Interface, ArrayAccess, Iterator, Countable
{
    
    /**
     * Project
     *
     * @var theProject
     */
    protected $_project;
    
    /**
     * List of issues
     *
     * Associative array where key is an issue unique ID and
     * value is an instance of Model_Asset_Defects_Issue_Abstract. If
     * value is false it means that the ticket is not YET loaded
     * and will be loaded soon, in lazy mode.
     *
     * @var ArrayIterator(Model_Asset_Defects_Issue_Abstract)
     * @see _getIssues()
     */
    protected $_issues = null;
    
    /**
     * Save project
     *
     * @param theProject Project
     * @return void
     */
    public function setProject(theProject $project) 
    {
        $this->_project = $project;
    }
    
    /**
     * Issue exists?
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param integer Id of the issue
     * @return boolean
     */
    public function offsetExists($id)
    {
        return $this->_getIssues()->offsetExists();
    }

    /**
     * Get one statement
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param integer Id of the payment
     * @return Model_Asset_Defects_Issue_Abstract
     */
    public function offsetGet($id) 
    {
        return $this->_load($id);
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     * @throws ProjectIssuesException
     */
    public function offsetSet($email, $value) 
    {
        FaZend_Exception::raise(
            'ProjectIssuesException', 
            "Issues are not editable directly by ID"
        );
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     * @throws ProjectIssuesException
     */
    public function offsetUnset($email) 
    {
        FaZend_Exception::raise(
            'ProjectIssuesException', 
            "Issues are not editable directly by ID"
        );
    }

    /**
     * Return current element
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function current() 
    {
        $this->_load($this->key());
        return $this->_getIssues()->current();
    }
    
    /**
     * Return next
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function next() 
    {
        $this->_load($this->key());
        return $this->_getIssues()->next();
    }
    
    /**
     * Return key
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function key() 
    {
        return $this->_getIssues()->key();
    }
    
    /**
     * Is valid?
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return boolean
     */
    public function valid() 
    {
        return $this->_getIssues()->valid();
    }
    
    /**
     * Rewind
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theStatement
     */
    public function rewind() 
    {
        return $this->_getIssues()->rewind();
    }
    
    /**
     * Count them
     * 
     * The method is required by Countable interface, don't delete it.
     *
     * @return theStatement
     */
    public function count() 
    {
        return $this->_getIssues()->count();
    }
    
    /**
     * Get array with issues ID's (integers)
     *
     * @return array()
     * @see $this->_issues
     **/
    protected function _getIssues()
    {
        if (!isset($this->_issues)) {
            $this->_issues = new ArrayIterator(
                array_map(
                    create_function('', 'return false;'),
                    array_flip($this->_project->fzProject()
                        ->getAsset(Model_Project::ASSET_DEFECTS)->retrieveBy())
                )
            );
        }
        return $this->_issues;
    }

    /**
     * Load issue 
     *
     * @param mixed ID of the issue
     * @return Model_Asset_Defects_Issue_Abstract
     * @throws ProjectIssuesNotFound
     */
    protected function _load($id) 
    {
        if (!$this->_getIssues()->offsetExists($id)) {
            FaZend_Exception::raise(
                'ProjectIssuesNotFound', 
                "Issue not found by id: {$id}"
            );
        }

        $issue = $this->_getIssues()->offsetGet($id);
        if ($issue !== false)
            return $issue;
            
        $issue = $this->_project->fzProject()
            ->getAsset(Model_Project::ASSET_DEFECTS)->findById($id);
        $this->_getIssues()->offsetSet($id, $issue);
        return $issue;
    }

}
