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

    /**
     * This project is managed by wobots?
     *
     * The project is managed if one of it's stakeholders is 'pm@wobot.net'
     *
     * @return boolean
     */
    public function isManaged() {
        $email = Model_Wobot::factory('PM.' . $this->name)->email;
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
            $matches = array();
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
     * @return string[]
     */
    public function getStakeholdersByRole($role) {
        // array_filter will remove people with FALSE (no access)
        return array_keys(array_filter($this->getAccessRights('/role/' . $role)));
    }
    
    /**
     * Name of the issue tracker for this given project
     *
     * @return string
     * @todo This is just a stub now, it should be configured
     **/
    protected function _getTracker() {
        return 'trac';
    }
    
}
