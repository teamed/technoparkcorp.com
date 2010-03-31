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
 * One project
 *
 * @package Model
 * @see Shared_Project
 */
class Model_Project extends Shared_Project
{
    
    const ROLE_AUTHZ_PREFIX = '/role/';
    
    // don't rename them, they are names of directories under Model/Asset
    const ASSET_SRS = 'Srs';
    const ASSET_DEFECTS = 'Defects';
    const ASSET_CODE = 'Code';
    const ASSET_DESIGN = 'Design';
    const ASSET_SUPPLIERS = 'Suppliers';
    const ASSET_OPPORTUNITIES = 'Opportunities';
    
    /**
     * We manage anything? If set to FALSE - none of the projects are managed
     *
     * @var boolean
     * @see isManaged()
     * @see setWeAreManaging()
     * @see getWeAreManaging()
     */
    protected static $_weAreManaging = true;
    
    /**
     * Shall we manage any projects?
     *
     * @param boolean Shall we?
     * @return void
     * @see $this->_weAreManaging
     * @see getWeAreManaging()
     * @see isManaged()
     */
    public static function setWeAreManaging($weAreManaging = true) 
    {
        self::$_weAreManaging = $weAreManaging;
    }

    /**
     * Shall we manage any projects?
     *
     * @return boolean
     * @see $this->_weAreManaging
     * @see setWeAreManaging()
     * @see isManaged()
     */
    public static function getWeAreManaging() 
    {
        return self::$_weAreManaging;
    }

    /**
     * This project is managed by wobots?
     *
     * The project is managed if one of it's stakeholders is '*@tpc2.com',
     * while the exact email depends on the particular project. Exact email
     * of the wobot you can get from Model_Wobot class, getEmail() method
     *
     * @return boolean
     * @see Model_Wobot_PM::__construct()
     */
    public function isManaged() 
    {
        if (!self::$_weAreManaging) {
            return false;
        }
        return in_array(
            Model_Wobot_PM::getEmailByProjectName($this->name),
            $this->_getWobots()
        );
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
     * @see theStaffAssignments
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
     * @see theStaffAssignments::retrieveRolesByStakeholder()
     */
    public function getRolesByStakeholder($email) 
    {
        $roles = array();
        foreach ($this->getAllowedPaths($email) as $path) {
            if (preg_match('/^' . preg_quote(self::ROLE_AUTHZ_PREFIX, '/') . '(\w+)$/i', $path, $matches))
                $roles[] = $matches[1];
        }
        return $roles;
    }
    
    /**
     * Get one asset
     *
     * @param string Name of the asset to get
     * @return Model_Asset_Abstract
     * @todo Should be configurable
     */
    public function getAsset($name) 
    {
        // we will be able later to configure which project
        // is using which holder of data
        $assets = array(
            self::ASSET_SRS => 'Fazend_Trac',
            self::ASSET_DEFECTS => 'Fazend_Trac',
            self::ASSET_CODE => 'Fazend_Pan',
            self::ASSET_DESIGN => 'Fazend_Linux',
            self::ASSET_SUPPLIERS => 'Fazend_Trac',
            self::ASSET_OPPORTUNITIES => 'Fazend_Trac',
        );

        // create a class according to the information above
        $className = "Model_Asset_{$name}_{$assets[$name]}";
        return FaZend_Flyweight::factoryById(
            $className, 
            $this->name . '.' . $name, 
            $this
        );
    }
    
    /**
     * Get list of wobots (emails), who have access to the project
     *
     * These emails are specified in FaZend platform, or maybe in some other
     * place, outside of thePanel.
     *
     * @return string[]
     * @see isManaged()
     */
    protected function _getWobots()
    {
        $list = array();
        foreach (array_keys($this->getStakeholders()) as $email) {
            if (preg_match('/^.*@' . preg_quote(Model_Wobot::EMAIL_DOMAIN, '/') . '$/i', $email)) {
                $list[] = strtolower($email);
            }
        }
        return $list;
    }

}
