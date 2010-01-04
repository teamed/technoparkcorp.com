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
        return FaZend_Flyweight::factory('Model_Issue_Trac', $this, $id);
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
     * Initializer
     *
     * @return void
     **/
    protected function _init() 
    {
        $this->_trac = new Shared_Trac($this->_project);
    }

}
