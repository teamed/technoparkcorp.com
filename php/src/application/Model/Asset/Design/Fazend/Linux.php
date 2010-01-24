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
 * Interface to Design elements
 *
 * @package Model
 */
class Model_Asset_Design_Fazend_Linux extends Model_Asset_Design_Abstract 
{
    
    /**
     * Shared_Pan connector
     *
     * @var Shared_Pan
     * @see _init()
     **/
    protected $_pan;
    
    /**
     * Get full list of components
     *
     * @return mixed[]
     **/
    public function getComponents() 
    {
        $list = $this->_pan->getComponents();
        $return = array();
        foreach ($list as $data) {
            $return[] = FaZend_StdObject::create()
                ->set('type', $data['type'])
                ->set('name', $data['fullName'])
                ->set('description', '...')
                ->set('traces', $data['traces'])
                ;
        }
        return $return;
    }
    
    /**
     * Initializer
     *
     * @return void
     **/
    protected function _init() 
    {
        $this->_pan = new Shared_Pan($this->_project);
    }

}
