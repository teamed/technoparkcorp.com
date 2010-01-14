<?php
/**
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
 * Interface to Design elements
 *
 * @package Model
 */
class Model_Asset_Design_Fazend_Linux extends Model_Asset_Design_Abstract 
{
    
    /**
     * XML RPC proxy
     *
     * @var Zend_XmlRpc_Client
     **/
    protected $_proxy;
    
    /**
     * Get full list of components
     *
     * @return mixed[]
     **/
    public function getComponents() 
    {
        $list = $this->_getProxy()->getAnalysis($this->_project->name);
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
     * Get XML RPC proxy
     *
     * @return Zend_XmlRpc_Client
     **/
    protected function _getProxy() 
    {
        if (!isset($this->_proxy)) {
            $xmlRpc = new Shared_XmlRpc($this->_project, $this->_project->user->email);
            $this->_proxy = $xmlRpc->client(
                'http://linux.fazend.com/pan',
                'pan'
            );
        }
        return $this->_proxy;
    }
    
}
