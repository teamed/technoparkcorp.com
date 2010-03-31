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
 * Interface to wiki SRS in Trac, at Fazend.com platform
 *
 * @package Model
 */
class Model_Asset_Defects_Fazend_Trac extends Model_Asset_Defects_Abstract
{
    
    const QUERY_ALL = 'order=id';
    
    /**
     * How many tickets we should retrieve per each page
     *
     * @var integer
     */
    protected static $_ticketsPerPage = 100;

    /**
     * Trac from Shared lib
     *
     * @var Shared_Trac
     */
    protected $_trac;
    
    /**
     * Set how many tickets we should retrieve per one page
     *
     * @param integer Tickets number
     * @return void
     */
    public static function setTicketsPerPage($ticketsPerPage) 
    {
        self::$_ticketsPerPage = $ticketsPerPage;
    }
    
    /**
     * Get one ticket by ID
     *
     * @return mixed
     * @see Model_Asset_Defects_Abstract::findById()
     */
    public function findById($id) 
    {
        return FaZend_Flyweight::factory(
            'Model_Asset_Defects_Issue_Trac',
            $this,
            false, // code
            $id // ID of the ticket
        );
    }
    
    /**
     * Get one ticket by CODE
     *
     * @return mixed
     * @see Model_Asset_Defects_Abstract::findByCode()
     */
    public function findByCode($code) 
    {
        return FaZend_Flyweight::factory(
            'Model_Asset_Defects_Issue_Trac', 
            $this, 
            $code
        );
    }
    
    /**
     * Get proxy
     *
     * @return mixed
     */
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
     * @return array List of ID's, integers
     * @throws Model_Asset_Defects_Fazend_Trac_SoapFault
     */
    public function retrieveBy(array $conditions = array(), array $negative = array())
    {
        $lemmas = array();
        foreach (array('=' => $conditions, '!=' => $negative) as $sign=>$list) {
            foreach ($list as $attrib=>$value) {
                validate()->alnum($attrib, 'Attributes should be alnum only');
                $lemmas[] = $attrib . $sign . urlencode($value);
            }
        }
        
        // we should not send empty queries to Trac
        if (!count($lemmas)) {
            $lemmas[] = self::QUERY_ALL;
        }

        $ids = array();
        $page = 1;
        do {
            try {
                $portion = $this->_trac->query(
                    implode('&', $lemmas) . '&max=' . self::$_ticketsPerPage . '&page=' . $page++, 
                    false
                );
            } catch (Zend_XmlRpc_Client_FaultException $e) {
                if (strpos($e->getMessage(), 'is beyond the number of pages') !== false)
                    break;
                FaZend_Exception::raise(
                    'Model_Asset_Defects_Fazend_Trac_SoapFault',
                    $e->getMessage()
                );
            }
            $ids = array_merge($ids, $portion);
            if (count($portion) < self::$_ticketsPerPage) {
                break;
            }
        } while (count($portion) > 0);
        return array_unique($ids);
    }
    
    /**
     * Get full list of all known ticket severities
     *
     * @return string[]
     */
    public function getSeverities()
    {
        return $this->_trac->getSeverities();
    }
    
    /**
     * Get full list of all known ticket statuses
     *
     * @return string[]
     */
    public function getStatuses()
    {
        return $this->_trac->getStatuses();
    }
    
    /**
     * Initializer
     *
     * @return void
     */
    protected function _init() 
    {
        $this->_trac = new Shared_Trac($this->_project);
    }

}
