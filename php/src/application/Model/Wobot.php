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
abstract class Model_Wobot implements Model_Wobot_Interface
{

    const EMAIL_DOMAIN = 'tpc2.com'; // domain to be used in all wobot's emails
    
    /**
     * List of wobots
     *
     * @var Model_Wobot[]
     */
    protected static $_wobots = array();

    /**
     * Get list of all wobots, of all types
     *
     * The method is called from CLI executor. This method uses other
     * STATIC methods of Model_Wobot_* classes, named like:
     * Model_Wobot_PM::getAllNames(). They should return names also as
     * strings, in the same format: "type.context".
     *
     * @return Model_Wobot[]
     */
    public static function retrieveAll() 
    {
        self::$_wobots = new ArrayIterator();
        
        // list all wobot names
        foreach (self::_getAllNames() as $name) {
            $wobotClass = __CLASS__ . '_' . $name;
            
            if (!method_exists($wobotClass, 'getAllNames')) {
                FaZend_Exception::raise(
                    'Model_Wobot_InvalidChild',
                    "Class '{$wobotClass}' has to implement '::getAllNames()'"
                );
            }
            
            eval("\$names = $wobotClass::getAllNames();");
            foreach ($names as $wobotName)
                self::$_wobots[$wobotName] = self::factory($wobotName);
        }
        // logg("Wobots found: " . implode(', ', array_keys(self::$_wobots->getArrayCopy())));
            
        return self::$_wobots;
    }

    /**
     * Factory method to create new specific wobot
     *
     * @param string Wobot name including context, like PM.ABC
     * @return Model_Wobot
     */
    public static function factory($name) 
    {
        // maybe it already exists?
        if (isset(self::$_wobots[$name]))
            return self::$_wobots[$name];
            
        if (strpos($name, '.') === false) {
            $type = $name;
            $context = null;
        } else {
            list($type, $context) = explode('.', $name);
        }

        $className = __CLASS__ . '_' . $type;
        return self::$_wobots[$name] = new $className($context);
    }

    /**
     * Text name of the wobot
     *
     * @return string
     */
    public function __toString() 
    {
        return $this->getFullName();
    }

    /**
     * Text name of the wobot
     *
     * @return string
     */
    public function getFullName() 
    {
        return $this->getName() . '.' . $this->getContext();
    }

    /**
     * Calculate name of the wobot
     *
     * @return string
     */
    public function getName() 
    {
        return str_replace(__CLASS__ . '_', '', get_class($this));
    }

    /**
     * Calculate email of the wobot (without domain, which is always self::EMAIL_DOMAIN)
     *
     * @return string
     */
    public function getEmailPrefix() 
    {
        return strtolower($this->getName());
    }

    /**
     * Calculate email of the wobot
     *
     * @return string
     */
    public function getEmail() 
    {
        return $this->getEmailPrefix() . '@' . self::EMAIL_DOMAIN;
    }
    
    /**
     * Get timezone when this wobot shall work
     *
     * The method returns an integer, which is a positive
     * or negative increment to GMT. If it returns +5 it means
     * that the wobot works in GMT+5 timezone.
     *
     * @return integer
     */
    public function getTimezone() 
    {
        // central european time for now
        return +1;
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
    public function getContext() 
    {
        return '';
    }
    
    /**
     * The wobot is in the office now? :)
     *
     * This method takes into account current time and the timezone
     * where the wobot is located.
     *
     * @return boolean
     */
    public function isInOfficeNow() 
    {
        $now = Zend_Date::now();
        
        // is it a weekend?
        $weekday = $now->get(Zend_Date::WEEKDAY_DIGIT);
        if (($weekday == 0) || ($weekday == 6))
            return false;
            
        $timezone = $this->getTimezone();
        $diff = $now->get(Zend_Date::TIMEZONE_SECS) / (60 * 60);
        $hour = $now->get(Zend_Date::HOUR) - $diff + $timezone;
        return ($hour >= 8) && ($hour <= 20);
    }

    /**
     * Execute this wobot (make next waiting decision)
     *
     * @return string|false The decision just made
     * @throws Model_Wobot_OutOfOfficeException
     */
    public function execute() 
    {
        if (!$this->isInOfficeNow()) {
            FaZend_Exception::raise(
                'Model_Wobot_OutOfOfficeException',
                "Wrong time to execute the wobot"
            );
        }
        $file = Model_Decision::nextForWobot($this);
        if (empty($file))
            return false;
        return $this->decisionFactory($file)->make();
    }

    /**
     * Create decision
     *
     * @param string Absolute file name of PHP file with decision class
     * @return Model_Decision
     **/
    public function decisionFactory($file) 
    {
        return Model_Decision::factory($file, $this);
    }

    /**
     * Returns a list of all possible wobot names (using /wobots directory)
     *
     * @return string[]
     **/
    protected static function _getAllNames() 
    {
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
