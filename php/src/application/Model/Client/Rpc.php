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
 * XML RPC client 
 *
 * @package Model
 */
class Model_Client_Rpc {

    /**
	 * Create new proxy
     *
     * @param Model_Project The project
     * @param string URL
     * @param string Name of proxy
     * @return Zend_XmlRpc_Client
     */
	public static function factory(Model_Project $project, $uri, $proxy = null) {
        // configure HTTP connector
        $httpClient = Model_Flyweight::factory('Zend_Http_Client', $uri, array());
        
        // current user gets access
        $login = Model_User::getCurrentUser()->email;
        $password = $project->getStakeholderPassword($login);
        $httpClient->setAuth($login, $password);

        // make connection
        $client = Model_Flyweight::factory('Zend_XmlRpc_Client', $uri, $httpClient);

        // get this particular proxy locator
        if ($proxy)
            return $client->getProxy($proxy);
            
        return $client;
    }

}
