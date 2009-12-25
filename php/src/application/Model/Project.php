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
class Model_Project extends Shared_Project
{
    
    const ROLE_AUTHZ_PREFIX = '/role/';
    
    // don't rename them, they are names of directories under Model/Asset
    const ASSET_SRS = 'Srs';
    const ASSET_DEFECTS = 'Defects';
    const ASSET_CODE = 'Code';
    const ASSET_SUPPLIERS = 'Suppliers';
    
    /**
     * We manage anything? If set to FALSE - none of the projects are managed
     *
     * @var boolean
     */
    protected static $_weAreManaging = true;
    
    /**
     * Shall we manage any projects?
     *
     * @param boolean Shall we?
     * @return void
     **/
    public static function setWeAreManaging($weAreManaging = true) 
    {
        self::$_weAreManaging = $weAreManaging;
    }

    /**
     * This project is managed by wobots?
     *
     * The project is managed if one of it's stakeholders is '*@tpc2.com',
     * while the exact email depends on the particular project. Exact email
     * of the wobot you can get from Model_Wobot class, getEmail() method
     *
     * @return boolean
     */
    public function isManaged() 
    {
        if (!self::$_weAreManaging)
            return false;
        return in_array(
            Model_Wobot_PM::getEmailByProjectName($this->name),
            $this->getWobots()
            );
    }


    /**
     * Get list of wobots (emails), who have access to the project
     *
     * These emails are specified in FaZend platform, or maybe in some other
     * place, outside of thePanel.
     *
     * @return string[]
     */
    public function getWobots()
    {
        $list = array();
        foreach ($this->getStakeholders() as $email=>$password) {
            if (preg_match('/^.*@' . preg_quote(Model_Wobot::EMAIL_DOMAIN, '/') . '$/i', $email))
                $list[] = strtolower($email);
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
    public function getStakeholdersByRole($role) 
    {
        // array_filter will remove people with FALSE (no access)
        return array_keys(array_filter($this->getAccessRights(self::ROLE_AUTHZ_PREFIX . $role)));
    }
    
    /**
     * Get list of all roles for the given email
     *
     * @param string Name of the role
     * @return string[]
     */
    public function getRolesByStakeholder($email) 
    {
        $roles = array();
        foreach ($this->getAllowedPaths($email) as $path) {
            if (preg_match('/^' . preg_quote(self::ROLE_AUTHZ_PREFIX, '/') . '(\w+)$/', $path, $matches))
                $roles[] = $matches[1];
        }
        return $roles;
    }
    
    /**
     * Get one asset
     *
     * @return Model_Asset_Abstract
     * @todo Should be configurable
     **/
    public function getAsset($name) 
    {
        // we will be able later to configure which project
        // is using which holder of data
        $assets = array(
            self::ASSET_SRS => 'Fazend_Trac',
            self::ASSET_DEFECTS => 'Fazend_Trac',
            self::ASSET_CODE => 'Fazend_Pan',
            self::ASSET_SUPPLIERS => 'Fazend_Trac',
            );

        // create a class according to the information above
        $className = "Model_Asset_{$name}_{$assets[$name]}";

        return new $className($this);
    }
    
}
