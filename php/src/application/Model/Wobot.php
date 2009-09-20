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
abstract class Model_Wobot extends FaZend_StdObject {

    const EMAIL_DOMAIN = 'wobot.net'; // domain to be used in all wobot's emails

    /**
     * Get list of all wobots
     *
     * The method is called from CLI executor.
     *
     * @return Model_Wobot[]
     * @todo Implement it properly, should use /wobots directory
     */
    public static function retrieveAll() {
        $wobots = array();
        
        // list all wobot names
        foreach (self::_getAllNames() as $name) {
            $wobotClass = __CLASS__ . '_' . $name;
            foreach ($wobotClass::getAllNames() as $wobotName)
                $wobots = self::factory($wobotName);
        }
            
        return new ArrayIterator($wobots);
    }

    /**
     * Factory method to create new specific wobot
     *
     * @param string Wobot name including context, like PM.ABC
     * @return Model_Wobot
     */
    public static function factory($name) {
        $exp = explode('.', $name);

        $className = __CLASS__ . '_' . $exp[0];
        return new $className(isset($exp[1]) ? $exp[1] : null);
    }

    /**
     * Text name of the wobot
     *
     * @return string
     */
    public function __toString() {
        return $this->fullName;
    }

    /**
     * Text name of the wobot
     *
     * @return string
     */
    protected function _getFullName() {
        return $this->name . '.' . $this->context;
    }

    /**
     * Calculate name of the wobot
     *
     * @return string
     */
    protected function _getName() {
        return str_replace(__CLASS__ . '_', '', get_class($this));
    }

    /**
     * Calculate email of the wobot (without domain, which is always self::EMAIL_DOMAIN)
     *
     * @return string
     */
    protected function _getEmailPrefix() {
        return strtolower($this->name);
    }

    /**
     * Calculate email of the wobot
     *
     * @return string
     */
    protected function _getEmail() {
        return $this->email . '@' . self::EMAIL_DOMAIN;
    }

    /**
     * Calculate context
     *
     * @return string
     */
    protected function _getContext() {
        return '';
    }

    /**
     * Execute this wobot (make next waiting decision)
     *
     * @return string The decision just made
     */
    public function execute() {
        return $this->_nextDecision()->make();
    }

    /**
     * Selects the next decision to be executed
     *
     * @return Model_Decision
     */
    protected function _nextDecision() {
        // return it, preconfigured
        return Model_Decision::factory($this->_nextDecisionFile(), $this);
    }

    /**
     * Selects the next decision to be executed
     *
     * @return string PHP file with next decision
     */
    protected function _nextDecisionFile() {

        // get list of all files in this wobot
        $files = $this->_getDecisionFiles();

        // find next decision to be made
        return Model_Decision_History::findNextDecision($this, $files);

    }

    /**
     * Get full list of wobot decision files
     *
     * @param string Path to find files in
     * @return string[]
     */
    protected function _getDecisionFiles($path = null) {
        if (is_null($path))
            $path = APPLICATION_PATH . '/wobots/' . $this->name;

        // get through all files in the directory and collect PHP decisions
        $files = array();
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file))
                $files = array_merge($files, $this->_getDecisionFiles($file));
            else
                $files[] = $file;
        }

        // return list of found PHP files
        return $files;
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
