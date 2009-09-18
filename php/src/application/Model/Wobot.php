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
 * @package Model
 */
abstract class Model_Wobot extends FaZend_StdObject {

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
        foreach (Model_Artifact::root()->projectRegistry as $name=>$project) {
            $wobots[] = self::factory('PM.' . $name);
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

        $className = 'Model_Wobot_' . $exp[0];
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
        return str_replace('Model_Wobot_', '', get_class($this));
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
     * Execute this wobot
     *
     * @return string Log of execution
     */
    public function execute() {
        $this->_nextDecision()->make();
    }

    /**
     * Selects the next decision to be executed
     *
     * @return Model_Decision
     */
    protected function _nextDecision() {

        // get list of all files in this wobot
        $files = $this->_getDecisionFiles();

        // find next decision to be made
        $file = Model_Decision_History::findNextDecision($this, $files);

        // return it, preconfigured
        return Model_Decision::factory($file, $this);

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

        $files = array();
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file))
                $files = array_merge($files, $this->_getDecisionFiles($file));
            else
                $files[] = $file;
        }

        return $files;
    }

}
