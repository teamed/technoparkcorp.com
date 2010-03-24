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
 * Wobots executor, to be called by CRONTAB every minute
 *
 * Executes one DECISION of every wobot and returns zero.
 *
 * @package CLI
 */
class Wobots extends FaZend_Cli_Abstract
{

    /**
     * Flag name, file absolute name
     *
     * @var string
     */
    protected $_flag = TEMP_PATH . '/wbts-busy-flag.txt';

    /**
     * Executor of a command-line command
     *
     * @return int Exit code
     */
    public function execute()
    {
        // if we're working with another wobot at the moment
        if ($this->_serverIsBusyNow()) {
            return self::RETURNCODE_OK;
        }
        
        foreach (Model_Wobot::retrieveAll() as $wobot) {
            if (!$wobot->isInOfficeNow()) {
                continue;
            }
            Model_User::logIn($wobot->getEmail());
            $wobot->execute();
        }
        
        // release server, the task is done
        $this->_releaseServer();
        
        return self::RETURNCODE_OK;
    }
    
    /**
     * Server is busy at the moment?
     *
     * If server is not busy, this method sets a busy flag for it,
     * and returns false.
     *
     * @return boolean
     * @see execute()
     * @throws Wobots_FlagCreationFailedException
     */
    protected function _serverIsBusyNow() 
    {
        if (@file_exists($this->_flag)) {
            return true;
        }
        if (@file_put_contents($this->_flag, getmypid()) === false) {
            FaZend_Exception::raise(
                'Wobots_FlagCreationFailedException',
                "Failed to create flag: '{$this->_flag}'"
            );
        }
        return false;
    }

    /**
     * Release server and allow other wobots to work
     *
     * @return void
     * @see execute()
     * @throws Wobots_FlagAbsentException
     * @throws Wobots_FlagUnlinkException
     */
    protected function _releaseServer() 
    {
        if (!@file_exists($this->_flag)) {
            FaZend_Exception::raise(
                'Wobots_FlagAbsentException',
                "Flag not found: '{$this->_flag}'"
            );
        }
        if (@unlink($this->_flag) === false) {
            FaZend_Exception::raise(
                'Wobots_FlagUnlinkException',
                "Failed to delete flag: '{$this->_flag}'"
            );
        }
    }

}
