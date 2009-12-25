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
 * One PM wobot
 *
 * @package Model
 */
class Model_Wobot_PM extends Model_Wobot {

    /**
     * Names of wobots, dependins on the FIRST letter of project name
     *
     * Associative array where key is a range of letters, like 'a-c' and
     * the value is first name plus last name.
     *
     * @var string[]
     */
    private static $_names = array(
        'a-p' => 'Alex Safonov', // a.safonov is his email
        'q-z' => 'Roman Satin', // r.satin is his email
    );

    /**
     * Project name
     *
     * @var Model_Project
     */
    protected $_project;

    /**
     * Initializer
     *
     * @return void
     * @throws Model_Wobot_PM_ProjectMissed
     */
    protected function __construct($context = null) 
    {
        try {
            $this->_project = Model_Project::findByName($context);
        } catch (Shared_Project_NotFoundException $e) {
            FaZend_Exception::raise('Model_Wobot_PM_ProjectMissed', 
                "Project '$context' is absent, can't initialize PM wobot");
        }
    }

    /**
     * Returns a list of all possible wobot names of this given type/class
     *
     * @return string[]
     **/
    public static function getAllNames() 
    {
        $list = array();
        foreach (Model_Artifact::root()->projectRegistry as $name=>$project) {
            $list[] = 'PM.' . $name;
        }
        return $list;
    }

    /**
     * Calculate context (name of the project)
     *
     * @return string
     */
    public function getContext() 
    {
        return (string)$this->_project->name;
    }

    /**
     * Calculate email of the wobot (without domain, which is always self::EMAIL_DOMAIN)
     *
     * @return string
     */
    public function getEmailPrefix() 
    {
        return self::_humanToEmailPrefix($this->getHumanName());
    }

    /**
     * Get the full name of the human-wobot
     *
     * @return string
     * @throws Model_Wobot_NameNotFound
     */
    public function getHumanName() 
    {
        return self::_projectToHumanName($this->getContext());
    }

    /**
     * Get email of wobot by given project name
     *
     * @param string Project name
     * @return string
     **/
    public static function getEmailByProjectName($projectName) 
    {
        return self::_humanToEmailPrefix(self::_projectToHumanName($projectName)) . 
            '@' . self::EMAIL_DOMAIN;
    }

    /**
     * Create decision
     *
     * @return Model_Decision
     **/
    public function decisionFactory($file) 
    {
        $decision = parent::decisionFactory($file);
        $decision->setProject(Model_Artifact::root()->projectRegistry[$this->getContext()]);
        return $decision;
    }
    
    /**
     * Convert project name to human name
     *
     * @param string Project name
     * @return string Name of the wobot, like "John Smith"
     * @throws Model_Wobot_NameNotFound
     **/
    protected static function _projectToHumanName($projectName) 
    {
        foreach (self::$_names as $regexp=>$name)
            if (preg_match('/^[' . $regexp . ']/i', $projectName))
                return $name;
                
        FaZend_Exception::raise('Model_Wobot_NameNotFound', 
            "Can find human name for project: '{$projectName}'");
    }

    /**
     * Convert human name to email prefix
     *
     * @param string Human name, like "John Smith"
     * @return string Email prefix, like "j.smith"
     **/
    protected static function _humanToEmailPrefix($name) 
    {
        list($first, $last) = explode(' ', strtolower($name));
        return $first[0] . '.' . $last;
    }

}
