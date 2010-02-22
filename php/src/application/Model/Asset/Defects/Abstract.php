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
 * Interface to defects tracking system
 *
 * @package Model
 */
abstract class Model_Asset_Defects_Abstract extends Model_Asset_Abstract
{
    
    /**
     * Get one ticket by ID
     *
     * Returns an object that has the following methods:
     *  - getId(): returns unique ID of the ticket
     *  - getAttributes(): returns an array of attributes
     *
     * @param integer Unique number of the ticket
     * @return mixed
     **/
    abstract public function findById($id);
    
    /**
     * Get one ticket by CODE (string)
     *
     * @param string Code of the ticket to find
     * @return mixed
     * @see findById()
     **/
    abstract public function findByCode($code);
    
    /**
     * Retrieve a list of ticket IDs that satisfy the conditions
     *
     * @param array Associative array of conditiions, where key is attribute and
     *  value is a required value of the given attribute.
     * @param array The same, but negative
     * @return int[]
     * @see findById() Tickets returned are objects, without types
     **/
    abstract public function retrieveBy(array $conditions = array(), array $negative = array());

    /**
     * Get full list of all known ticket severities
     *
     * @return string[]
     **/
    abstract public function getSeverities();
    
    /**
     * Get full list of all known ticket statuses
     *
     * @return string[]
     **/
    abstract public function getStatuses();
    
}
