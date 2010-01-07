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
 * Interface to wiki SRS in Trac, at Fazend.com platform
 *
 * @package Model
 */
class Model_Asset_Defects_Fazend_Trac extends Model_Asset_Defects_Abstract 
{
    
    const QUERY_ALL = 'order=priority';

    /**
     * Trac from Shared lib
     *
     * @var Shared_Trac
     **/
    protected $_trac;
    
    /**
     * Get one ticket by ID
     *
     * @return mixed
     * @see Model_Asset_Defects_Abstract::findById()
     **/
    public function findById($id) 
    {
        return FaZend_Flyweight::factory('Model_Asset_Defects_Issue_Trac', $this, $id);
    }
    
    /**
     * Get proxy
     *
     * @return ...
     **/
    public function getXmlProxy()
    {
        return $this->_trac->getXmlProxy();
    }
    
    /**
     * Retrieve a list of tickets that satisfy the conditions
     *
     * @param array Associative array of conditiions, where key is attribute and
     *              value is a required value of the given attribute.
     * @param array The same, but negative
     * @return array
     **/
    public function retrieveBy(array $conditions = array(), array $negative = array())
    {
        $lemmas = array();
        foreach (array('=' => $conditions, '!=' => $negative) as $sign=>$list) {
            foreach ($list as $attrib=>$value) {
                validate()->alnum($attrib, 'Attributes should be alnum only');
                $lemmas[] = $attrib . $sign . "'" . addslashes($value) . "'";
            }
        }
        
        // we should not send empty queries to Trac
        if (!count($lemmas))
            $lemmas[] = self::QUERY_ALL;
            
        return $this->_trac->query(implode('&', $lemmas));
    }
    
    /**
     * Get full list of all known ticket severities
     *
     * @return string[]
     **/
    public function getSeverities()
    {
        return $this->_trac->getSeverities();
    }
    
    /**
     * Get full list of all known ticket statuses
     *
     * @return string[]
     **/
    public function getStatuses()
    {
        return $this->_trac->getStatuses();
    }
    
    /**
     * Initializer
     *
     * @return void
     **/
    protected function _init() 
    {
        $this->_trac = new Shared_Trac($this->_project);
    }

}
