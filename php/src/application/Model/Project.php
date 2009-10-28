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
 * One project
 *
 * @package Model
 */
class Model_Project extends Shared_Project {
    
    const ROLE_AUTHZ_PREFIX = '/role/';

    /**
     * Finds project by its name or create one if it's a test project
     *
     * @return Model_Project
     **/
    public static function findProjectByName($name) {
        if (($name === Model_Project_Test::NAME) && (APPLICATION_ENV !== 'production'))
            return Model_Project_Test::make();
        return self::findByName($name);
    }

    /**
     * This project is managed by wobots?
     *
     * The project is managed if one of it's stakeholders is '*@wobot.net',
     * while the exact email depends on the particular project. Exact email
     * of the wobot you can get from Model_Wobot class, getEmail() method
     *
     * @return boolean
     */
    public function isManaged() {
        $email = Model_Wobot::factory('PM.' . $this->name)->getEmail();
        return in_array($email, $this->getWobots());
    }


    /**
     * Get list of wobots (emails)
     *
     * @return string[]
     */
    public function getWobots() {
        $list = array();
        foreach ($this->getStakeholders() as $email=>$password) {
            if (preg_match('/^(.*@' . preg_quote(Model_Wobot::EMAIL_DOMAIN) . ')$/', $email, $matches))
                $list[] = strtolower($matches[1]);
        }
        return $list;
    }

    /**
     * Get list of emails for a given role
     *
     * Roles should be defined in authz file, like:
     *
     * <code>
     * [project:/role/CCB]
     * pm@wobot.net = rw
     *
     * [project:/role/PM]
     * john@tpc2.com = r
     * </code>
     *
     * @param string Name of the role
     * @return string[]
     */
    public function getStakeholdersByRole($role) {
        // array_filter will remove people with FALSE (no access)
        return array_keys(array_filter($this->getAccessRights(self::ROLE_AUTHZ_PREFIX . $role)));
    }
    
    /**
     * Get list of all roles for the given email
     *
     * @param string Name of the role
     * @return string[]
     */
    public function getRolesByStakeholder($email) {
        $roles = array();
        foreach ($this->getAllowedPaths($email) as $path) {
            if (preg_match('/^' . preg_quote(self::ROLE_AUTHZ_PREFIX, '/') . '(\w+)$/', $path, $matches))
                $roles[] = $matches[1];
        }
        return $roles;
    }
    
    /**
     * Issue tracker for this project
     *
     * @return Model_Issue_Tracker_Abstract
     * @todo Should be configurable
     **/
    public function getTracker() {
        return Model_Issue_Tracker_Abstract::factory('trac', $this);
    }
    
    /**
     * Wiki holder for this project
     *
     * @return Model_Wiki_Abstract
     * @todo Should be configurable
     **/
    public function getWiki() {
        return Model_Wiki_Abstract::factory('trac', $this);
    }
    
    /**
     * Pan facade for source code
     *
     * @return Zend_XmlRpc_Client
     * @todo Should be configurable
     **/
    public function getPan() {
        return Model_Client_Rpc::factory(
            $this,
            'http://linux.fazend.com/p/' . $this->_project->name . '/trunk/__fz/xmlrpc');
    }
    
}
