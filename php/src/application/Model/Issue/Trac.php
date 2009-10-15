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
 * One issue in Trac
 *
 * @package Model
 */
class Model_Issue_Trac extends Model_Issue_Abstract {

    /**
     * Issue really exists in tracker?
     *
     * @return integer ID of this issue in Trac
     **/
    public function exists() {
        // if TRAC id already exists in the class - we are sure that issue exists in trac
        if ($this->_id)
            return true;
        
        // if we already checked this issue and know that it's absent
        if ($this->_id === false)
            return false;
        
        // get list of IDs with this code (we expect JUST ONE)
        $ids = $this->_proxy()->query('code=' . $this->code);
        logg("Query to Trac for code '{$this->code}' returned " . count($ids) . ' ticket');
        
        // nothing or something strange
        if (count($ids) != 1)
            return $this->_id = false;
    
        // remember found ID in the class and return it
        return $this->_id = array_pop($ids);
    }
        
    /**
     * Load changelog data
     *
     * @return void
     **/
    protected function _loadChangelog() {
        $log = $this->_proxy()->changeLog($this->_id);
        logg("Issue '{$this->_id}' has " . count($log) . ' changes (from changelog)');
        
        $fields = array();
        $records = array();
        foreach ($log as $record) {
            $records[] = array($record[2], $record[4], $record[1], $record[0]);
            $fields[$record[2]] = true;
        }
        
        $details = $this->_proxy()->get($this->_id);
        
        foreach ($details[3] as $k=>$v) {
            if (!isset($fields[$k]))
                $records[] = array($k, $v, $details[3]['reporter'], $details[1]);
        }

        // bug($log);
        foreach ($records as $record) {
            list($name, $value, $author, $date) = $this->_translateFromTrac(
                $record[0], // name of field
                $record[1], // value of this field
                $record[2], // author of changes
                $record[3] // date of change
                );
                
            if (!$this->_changelog->allowsField($name))
                continue;
            $this->_changelog->load($name, $value, $author, $date);
        }
    }
        
    /**
     * Save all changes made in changelog and create issue before, if necessary
     *
     * @return void
     **/
    protected function _saveChangelog() {
        // maybe it's alive already?
        if (!$this->exists()) {
            // create new ticket in trac
            $id = $this->_rpc()->create(
                (string)$this->_changelog->get('summary')->getValue(),
                (string)$this->_changelog->get('description')->getValue(),
                $this->_translateToTrac($this->_changelog->whatToSave()),
                false);
            
            logg("Trac ticket #$id created");
            $this->_id = $id;
        } else {
            //...
        }
    }

    /**
     * Return XML RPC proxy for tickets in Trac
     *
     * @return Zend_XmlRpc_Client
     **/
    protected function _proxy() {
        return $this->_tracker->getXmlRpcTicketProxy();
    }
    
    /**
     * Translate what we got from Trac to our changelog-suitable pair
     *
     * @param string Name of the field
     * @param string Value of the field
     * @param string Author of the field
     * @param string Date of change of the field
     * @return array
     **/
    protected function _translateFromTrac($name, $value, $author, $date) {
        switch ($name) {
            
            // we ignore them
            case 'code':
            case 'cc':
            case 'keywords':
            case 'milestone':
            case 'version':
                $name = null;
                break;
                
            case 'resolution':
                switch ($value) {
                    case 'fixed':
                        $this->_resolutionCache = Model_Issue_Changelog_Field_Status::FIXED;
                        break;
                    default: 
                        $this->_resolutionCache = Model_Issue_Changelog_Field_Status::INVALID;
                }
                break;

            case 'status':
                switch ($value) {
                    case 'closed':
                        $value = $this->resolutionCache;
                    default: 
                        $value = Model_Issue_Changelog_Field_Status::OPEN;
                }
                break;
        
            // type of ticket
            case 'type':
                switch ($value) {
                    case 'task':
                        $value = Model_Issue_Changelog_Field_Type::TASK;
                    default: 
                        $value = Model_Issue_Changelog_Field_Type::DEFECT;
                }
                break;

            // priority of ticket
            case 'priority':
                switch ($value) {
                    case 'major':
                        $value = Model_Issue_Changelog_Field_Priority::MINOR;
                    case 'critical':
                        $value = Model_Issue_Changelog_Field_Priority::CRITICAL;
                    case 'blocker':
                        $value = Model_Issue_Changelog_Field_Priority::BLOCKER;
                    default: 
                        $value = Model_Issue_Changelog_Field_Priority::MAJOR;
                }
                break;

            case 'component':
            case 'summary':
            case 'owner':
            case 'reporter':
            case 'description':
            case 'comment':
                $value = (string)$value;
                if (!$value)
                    $name = null;
                break;

            case 'cost':
                $value = (int)$value;
                break;
        
            case 'duration':
                $value = array_search($value, Shared_Trac::getDurationOptions());
                if ($value === false)
                    $value = 20; // 20 days if Trac value is not clear
                break;

            default:
                FaZend_Exception::raise('Model_Issue_Trac_UnknownField',
                    "Unknown field came from Trac: '{$name}', value: '{$value}'");
        
        }
        
        return array(
            (string)$name, 
            $value, 
            (string)$author, 
            strtotime($date)
            );
    }
    
    /**
     * Convert our internal fields to Trac
     *
     * @param array List of pairs to be sent to trac
     * @return array
     **/
    protected function _translateToTrac(array $pairs) {
        return $pairs;
    }
        
}
