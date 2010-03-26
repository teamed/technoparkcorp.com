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
     * @throws Model_Asset_Design_Fazend_Linux_SoapFailure
     * @throws Model_Asset_Design_Fazend_Linux_MissedPropertyException
     */
    public function getComponents() 
    {
        try {
            $list = $this->_pan->getComponents();
        } catch (Shared_Pan_SoapFailure $e) {
            FaZend_Exception::raise(
                'Model_Asset_Design_Fazend_Linux_SoapFailure',
                $e->getMessage()
            );
        }
        
        $return = array();
        foreach ($list as $data) {
            $type = $data['type'];
            switch (true) {
                case $type == 'category':
                case $type == 'package':
                    $data['type'] = 'package';
                    break;
                case $type == 'file':
                case preg_match('/^file_\w+File$/', $type):
                    $data['type'] = 'file';
                    break;
                case $type == 'method':
                    $data['type'] = 'method';
                    break;
                default:
                    $data['type'] = 'class';
                    break;
            }

            // validate data received
            foreach (array('type', 'fullName', 'todo', 'traces') as $property) {
                if (!array_key_exists($property, $data)) {
                    FaZend_Exception::raise(
                        'Model_Asset_Design_Fazend_Linux_MissedPropertyException',
                        "Property '{$property}' not found in data, project {$this->_project->name}"
                    );
                }
            }
            
            $return[] = FaZend_StdObject::create()
                ->set('type', $data['type'])
                ->set('name', $data['fullName'])
                ->set('description', '...')
                ->set('todoTickets', array_unique($data['todo']))
                ->set('traces', $data['traces']);
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
