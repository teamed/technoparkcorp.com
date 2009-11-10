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
 * One wobot
 * 
 * Abstract class, acting as a dispatcher and a general parent for
 * all wobots.
 *
 * @package Model
 */
abstract class Model_Wobot implements Model_Wobot_Interface {

    const EMAIL_DOMAIN = 'wobot.net'; // domain to be used in all wobot's emails
    
    /**
     * List of wobots
     *
     * @var Model_Wobot[]
     */
    protected static $_wobots = array();

    /**
     * Get list of all wobots
     *
     * The method is called from CLI executor.
     *
     * @return Model_Wobot[]
     */
    public static function retrieveAll() {
        self::$_wobots = new ArrayIterator();
        
        // list all wobot names
        foreach (self::_getAllNames() as $name) {
            $wobotClass = __CLASS__ . '_' . $name;
            foreach ($wobotClass::getAllNames() as $wobotName)
                self::$_wobots[$wobotName] = self::factory($wobotName);
        }
            
        return self::$_wobots;
    }

    /**
     * Factory method to create new specific wobot
     *
     * @param string Wobot name including context, like PM.ABC
     * @return Model_Wobot
     */
    public static function factory($name) {
        if (isset(self::$_wobots[$name]))
            return self::$_wobots[$name];
            
        $exp = explode('.', $name);

        $className = __CLASS__ . '_' . $exp[0];
        return self::$_wobots[$name] = new $className(isset($exp[1]) ? $exp[1] : null);
    }

    /**
     * Text name of the wobot
     *
     * @return string
     */
    public function __toString() {
        return $this->getFullName();
    }

    /**
     * Text name of the wobot
     *
     * @return string
     */
    public function getFullName() {
        return $this->getName() . '.' . $this->getContext();
    }

    /**
     * Calculate name of the wobot
     *
     * @return string
     */
    public function getName() {
        return str_replace(__CLASS__ . '_', '', get_class($this));
    }

    /**
     * Calculate email of the wobot (without domain, which is always self::EMAIL_DOMAIN)
     *
     * @return string
     */
    public function getEmailPrefix() {
        return strtolower($this->getName());
    }

    /**
     * Calculate email of the wobot
     *
     * @return string
     */
    public function getEmail() {
        return $this->getEmailPrefix() . '@' . self::EMAIL_DOMAIN;
    }

    /**
     * Get the full name of the human-wobot
     *
     * @return string
     */
    abstract public function getHumanName();
    
    /**
     * Calculate context
     *
     * @return string
     */
    public function getContext() {
        return '';
    }

    /**
     * Execute this wobot (make next waiting decision)
     *
     * @return string The decision just made
     */
    public function execute() {
        return Model_Decision::nextForWobot($this)->execute();
    }

    /**
     * Create decision
     *
     * @param string Absolute file name of PHP file with decision class
     * @return Model_Decision
     **/
    public function decisionFactory($file) {
        return Model_Decision::factory($file, $this);
    }

    /**
     * Returns a list of all possible wobot names (using /wobots directory)
     *
     * @return string[]
     **/
    protected static function _getAllNames() {
        $dir = APPLICATION_PATH . '/wobots';
        $list = array();
        foreach (scandir($dir) as $file) {
            if ($file[0] == '.')
                continue;
            $list[] = $file;
        }
        return $list;
    }

}
