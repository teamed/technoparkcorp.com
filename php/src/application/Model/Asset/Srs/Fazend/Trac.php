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
class Model_Asset_Srs_Fazend_Trac extends Model_Asset_Srs_Abstract
{
    
    /**
     * Execute RQDQL query and return a result
     *
     * @param string Query
     * @return SimpleXMLElement
     * @see Model_Asset_Srs_Abstract::rqdqlQuery()
     */
    public function rqdqlQuery($query)
    {
        $wiki = new Shared_Wiki($this->_project);
        return $wiki->rqdqlQuery($query);
    }
    
    
    /**
     * Get full list of SRS entities
     *
     * @see Shared_Wiki_Entity
     * @return Shared_Wiki_Entity[]
     * @see Model_Asset_Srs_Abstract::getEntities()
     */
    public function getEntities()
    {
        $wiki = new Shared_Wiki($this->_project);
        return $wiki->retrieveAll();
    }
    
}
