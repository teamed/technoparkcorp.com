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
 * Trac tracker
 *
 * @package Model
 */
class Model_Issue_Tracker_Trac extends Model_Issue_Tracker_Abstract {

    /**
     * The project related to this Trac
     *
     * @var Model_Project
     */
    protected $_project;
    
    /**
     * Client for Trac
     *
     * @var Zend_XmlRpc_Client
     */
    protected $_xmlRpc;

    /**
	 * Construct the class
     *
     * @param Model_Project The project, owner of this trac
     * @return void
     */
	public function __construct(Model_Project $project) {
	    $this->_project = $project;
	}

    /**
     * Issue really exists in tracker?
     *
     * @param Model_Issue_Abstract The issue to check
     * @return boolean
     **/
    public function issueExists(Model_Issue_Abstract $issue) {
        // if TRAC id already exists in the class - we are sure that issue exists in trac
        if ($issue->getId())
            return true;
        
        // if we already checked this issue and know that it's absent
        if ($issue->getId() === false)
            return false;
        
        // get list of IDs with this code (we expect JUST ONE)
        $ids = $this->_rpc()->query('code=' . $issue->getCode());
        logg("Query to Trac for code '{$issue->getCode()}' returned " . count($ids) . ' ticket');
        
        // nothing or something strange
        if (count($ids) != 1) {
            $issue->setId(false);
            return false;
        }
    
        // remember found ID in the class
        $issue->setId(array_pop($ids));
        
        // return success
        return true;
    }
        
    /**
     * List of messages in this issue
     *
     * @param Model_Issue_Abstract The issue to check
     * @return Model_Issue_Message_Abstract
     **/
    public function getIssueMessages(Model_Issue_Abstract $issue) {
        if (!$this->issueExists($issue))
            FaZend_Exception::raise('Model_Issue_Tracker_Trac', 
                "Issue {$issue->name} is absent in Trac");
                
        $log = $this->_rpc()->changeLog($issue->getId());
        
        return $log;
    }
        
    /**
     * Make sure that this issue exists in trac
     *
     * @param Model_Issue_Abstract The issue to check
     * @return void
     **/
    public function makeIssueAlive(Model_Issue_Abstract $issue) {
        // maybe it's alive already?
        if ($this->issueExists($issue))
            return;
                
        // create new ticket in trac
        $id = $this->_rpc()->create(
            $issue->getField('summary'),
            $issue->getField('description'),
            array(
                'reporter' => Model_User::getCurrentUser()->email, 
                'code' => $issue->getCode(),
                ), 
            false);
            
        logg("Trac ticket #$id created: '{$issue->getField('summary')}'");
        $issue->setId($id);
        
        return $log;
    }
        
    /**
     * Get XML RPC client instance
     *
     * @return Zend_XmlRpc_Client
     **/
    protected function _rpc() {
        if (!isset($this->_xmlRpc)) {
            $uri = 'http://trac.fazend.com/' . $this->_project->name . '/xmlrpc';

            // configure HTTP connector
            $httpClient = new Zend_Http_Client($uri, array());
            $login = $this->_project->user->email;
            $password = $this->_project->user->password;
            
            $httpClient->setAuth($login, $password);
            // make connection
            $client = new Zend_XmlRpc_Client($uri, $httpClient);

            // get this particular proxy locator
            $this->_xmlRpc = $client->getProxy('ticket');
                
            try {
                $fields = $this->_xmlRpc->getTicketFields();
            } catch (Exception $e) {
                FaZend_Exception::raise('Model_Issue_Tracker_Trac_FailedConnection',
                    "Can't connect: login '{$login}', password: '{$password}', URI: '{$uri}'. " . 
                    get_class($e) . ": '" . $e->getMessage() . "'");
            }
            
        }
        return $this->_xmlRpc;
    }

}
