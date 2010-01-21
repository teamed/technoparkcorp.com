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
 * History of decisions
 *
 * @package Model
 */
class Model_Decision_History extends FaZend_Db_Table_ActiveRow_history
{

    /**
     * Create new row
     *
     * @param Model_Wobot Wobot that initiated this decision
     * @param Model_Decision Decision that has to be protocoled
     * @return Model_Decision_History
     */
    public static function create(Model_Wobot $wobot, Model_Decision $decision)
    {
        $row = new self();
        $row->wobot = $wobot->getName();
        $row->context = $wobot->getContext();
        $row->result = getmypid() . ': ' . Zend_Date::now();
        $row->protocol = '';
        $row->hash = $decision->getHash();
        $row->save();

        return $row;
    }
    
    /**
     * undocumented function
     *
     * @param string|false Decision made
     * @param string Log of the decision made
     * @return void
     */
    public function recordResult($result, $log) 
    {
        $this->result = $result;
        $this->protocol = $log;
        $this->save();
    }

    /**
     * Find one decision history record
     *
     * @return Model_Decision_History
     **/
    public static function findByHash($hash)
    {
        return self::retrieve()
            ->where('hash = ?', $hash)
            ->order('created DESC')
            ->limit(1)
            ->setRowClass('Model_Decision_History')
            ->fetchRow();
    }

    /**
     * Find next waiting decision
     *
     * @param Model_Wobot Wobot that is looking for new decision
     * @param array List of files that exist in the wobot
     * @return string|null Name of the file or NULL if nothing found
     */
    public static function findNextDecision(Model_Wobot $wobot, array $files)
    {
        // keep them in alphabetic order always, to make sure we
        // execute them all, and don't lose any one
        sort($files);

        // the list is NOT empty
        validate()->true(count($files) > 0);

        // first element of the list - we return it if nothing better found
        $first = $files[0];

        // latest decision made by this wobot
        $latest = self::retrieve()
            ->setSilenceIfEmpty()
            ->where('wobot = ?', $wobot->getName())
            ->where('context = ?', $wobot->getContext())
            ->where('protocol <> ""') // they are still executing?
            ->order('created DESC')
            ->fetchRow();

        // get list of running decisions
        $running = self::retrieve()
            ->where('wobot = ?', $wobot->getName())
            ->where('context = ?', $wobot->getContext())
            ->where('protocol = ""') // they are still executing?
            ->fetchAll();

        if (!$latest)
            return $first;

        // exclude files that are executing NOW
        foreach ($running as $decision) {
            foreach ($files as $id=>$file) {
                if ($decision->hash == Model_Decision::hash($file)) {
                    unset($files[$id]);
                }
            }
        }
        
        if (!count($files))
            return null;

        while (count($files)) {
            $file = array_shift($files);

            // we found the file that ALREADY has been executed
            if ($latest->hash == Model_Decision::hash($file)) {
                if (count($files))
                    return $files[0];
                else
                    return $first;
            }
        }

        // nothing found in the list - return first element
        return $first;
    }

    /**
     * Retrieve all decisions for this particular wobot and context
     * 
     * @param Model_Wobot Wobot to work with
     * @return Model_Decision_History[]
     */
    public static function retrieveByWobot(Model_Wobot $wobot)
    {
        return self::retrieve()
            ->where('wobot = ?', $wobot->getName())
            ->where('context = ?', $wobot->getContext())
            ->order('created DESC')
            ->setRowClass('Model_Decision_History')
            ->fetchAll();
    }

    /**
     * Retrieve all decisions for this particular wobot and context, non empty
     * 
     * @param Model_Wobot Wobot to work with
     * @return Model_Decision_History[]
     */
    public static function retrieveByWobotNonEmpty(Model_Wobot $wobot)
    {
        return self::retrieve()
            ->where('wobot = ?', $wobot->getName())
            ->where('context = ?', $wobot->getContext())
            ->where('result <> ?', '')
            ->order('created DESC')
            ->setRowClass('Model_Decision_History')
            ->fetchAll();
    }

    /**
     * Clean all decisions made by wobot
     * 
     * @param Model_Wobot Wobot to work with
     * @return void
     */
    public static function cleanByWobot(Model_Wobot $wobot)
    {
        self::retrieve()
            ->where('wobot = ?', $wobot->getName())
            ->where('context = ?', $wobot->getContext())
            ->where('protocol <> ""')
            ->delete();
    }

    /**
     * This decision of this type is running now?
     *
     * @param Model_Wobot Wobot that initiated this decision
     * @param Model_Decision Decision that has to be protocoled
     * @return boolean
     */
    public static function hasRunning(Model_Wobot $wobot, Model_Decision $decision)
    {
        $history = self::retrieve()
            ->setSilenceIfEmpty()
            ->where('wobot = ?', $wobot->getName())
            ->where('hash = ?', $decision->getHash())
            ->where('context = ?', $wobot->getContext())
            ->where('protocol = ""')
            ->setRowClass('Model_Decision_History')
            ->fetchRow();
            
        if (!$history)
            return false;
        return $history->isRunning();
    }

    /**
     * This history record is about a running decision?
     *
     * @return boolean
     */
    public function isRunning() 
    {
        return (bool)$this->getPid();
    }
    
    /**
     * Get process ID if running
     *
     * @return null|integer
     */
    public function getPid() 
    {
        if (!empty($this->protocol))
            return null;
            
        if (!preg_match('/^(\d+):\s.*$/', $this->result, $matches)) {
            $this->protocol = 'result line is invalid';
            $this->save();
            return null;
        }
        
        $pid = intval($matches[1]);
        if (!shell_exec("ps -p {$pid} | grep {$pid}")) {
            $this->protocol = $this->getProtocol() . 
                "\n\nprocess {$pid} is not running any more\n";
            $this->save();
            return null;
        }
        
        return $pid;
    }

    /**
     * Title of the decision
     *
     * @return string
     */
    public function getTitle()
    {
        $title = substr($this->hash, 0, strpos($this->hash, '/'));
        return preg_replace('/([A-Z])/', ' ${1}', $title);
    }
    
    /**
     * Get protocol, even if it's not stopped yet
     *
     * @return string
     */
    public function getProtocol() 
    {
        if (!empty($this->protocol))
            return $this->protocol;
            
        $file = $this->getLogFileName();
        return "...getting it from {$file}...\n" . @file_get_contents();
    }
    
    /**
     * Get file name of a log file to be used with this decision
     *
     * @return string Absolute file name
     */
    public function getLogFileName() 
    {
        $dir = TEMP_PATH . '/panel2-decisions';
        if (!file_exists($dir) || !is_dir($dir))
            mkdir($dir);
        $file = $dir . '/' . substr(strrchr($this->hash, '/'), 1) . '.log';
        if (!file_exists($file))
            @file_put_contents($file, '');
        return $file;
    }
    
    /**
     * This decision finished with error?
     *
     * @return boolean
     */
    public function isError() 
    {
        return stripos($this->result, 'error') === 0;
    }

}
