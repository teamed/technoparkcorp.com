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
 * Collection of sales opportunities
 *
 * @package Artifacts
 * @property _opportunities Holds a collection of opps
 */
class theOpportunityRegistry extends Model_Artifact_Bag 
    implements ArrayAccess, Iterator, Countable, Model_Artifact_Passive, Model_Artifact_Interface
{

    /**
     * The list is loaded? It is always loaded, meaning that only explicit reloading may reload it
     *
     * @return true
     **/
    public function isLoaded() 
    {
        return true;
    }

    /**
     * Reload list of opportunities
     *
     * @return void
     **/
    public function reload() 
    {
        $opportunities = $this->_getOpportunities();
        foreach ($this->_getAsset()->retrieveAll() as $id) {
            $opportunities[$id] = false;
        }
    }
    
    /**
     * opportunity exists?
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string Name of the statement (email)
     * @return boolean
     */
    public function offsetExists($email) 
    {
        $this->_getOpportunities()->offsetExists($email);
    }

    /**
     * Get one opportunity
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @param string opportunity's email
     * @return theOpportunity
     * @see reload()
     */
    public function offsetGet($id) 
    {
        $opportunities = $this->_getOpportunities();
        if (!isset($opportunities[$id])) {
            FaZend_Exception::raise(
                'OpportunityRegistryNotFound', 
                "Opportunity '{$id}' not found in list (" . count($opportunities) . ' total)'
            );
        }
        
        if ($opportunities[$id] === false) {
            $opportunity = new theOpportunity($id);
            $asset = $this->_getAsset();
            
            $asset->deriveById($id, $opportunity);
            $opportunities[$id] = $opportunity;
            
            // make sure the registry is dirty now and will be saved to POS
            $this->ps()->setDirty();
        }

        return $opportunities[$id];
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     * @throws OpportunityRegistryException
     */
    public function offsetSet($email, $value) 
    {
        FaZend_Exception::raise(
            'OpportunityRegistryException', 
            "Opportunities are not editable directly"
        );
    }

    /**
     * This method is required by ArrayAccess, but is forbidden
     * 
     * The method is required by ArrayAccess interface, don't delete it.
     *
     * @return void
     * @throws OpportunityRegistryException
     */
    public function offsetUnset($email) 
    {
        FaZend_Exception::raise(
            'OpportunityRegistryException', 
            "Opportunities are not editable directly"
        );
    }

    /**
     * Return current element
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * Lazy-loading mechanism is implemented here. We have only keys (as
     * emails) and not real-life objects.
     *
     * @return theOpportunity
     * @see reload()
     */
    public function current() 
    {
        return $this->offsetGet($this->key());
    }
    
    /**
     * Return next
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * The method is intentionally designed like this, in order to implement
     * lazy-loading of opportunities in the array. When reload() creates an array
     * of opportunities - it sets FALSE to all of them. And later we can build them
     * using array keys as their emails. This lazy-loading mechanism also
     * affects current() method.
     *
     * @return theOpportunity
     * @see reload()
     * @see current()
     */
    public function next() 
    {
        // maybe it's the end
        if (!$this->_getOpportunities()->next()) {
            return false;
        }
            
        return $this->offsetGet($this->key());
    }
    
    /**
     * Return key
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return theOpportunity
     */
    public function key() 
    {
        return $this->_getOpportunities()->key();
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
        return $this->_getOpportunities()->valid();
    }
    
    /**
     * Rewind
     * 
     * The method is required by Iterator interface, don't delete it.
     *
     * @return void
     */
    public function rewind() 
    {
        return $this->_getOpportunities()->rewind();
    }
    
    /**
     * Count them
     * 
     * The method is required by Countable interface, don't delete it.
     *
     * @return integer
     */
    public function count() 
    {
        return $this->_getOpportunities()->count();
    }

    /**
     * Get a list of opportunities, internal holder
     *
     * @return theOpportunity[]
     **/
    protected function _getOpportunities() 
    {
        if (!isset($this->_opportunities))
            $this->_opportunities = new ArrayIterator();
        return $this->_opportunities;
    }
    
    /**
     * Get ASSET for opps management
     *
     * @return void
     */
    protected function _getAsset() 
    {
        return Model_Project::findByName('Sales')
            ->getAsset(Model_Project::ASSET_OPPORTUNITIES);
    }
    
}
