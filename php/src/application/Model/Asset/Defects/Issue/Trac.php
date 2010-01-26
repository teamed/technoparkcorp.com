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
 * One issue in Trac
 *
 * @package Model
 */
class Model_Asset_Defects_Issue_Trac extends Model_Asset_Defects_Issue_Abstract 
{
    
    /**
     * Cache of resolution
     *
     * @var string
     * @see _translateFromTrac()
     */
    protected $_resolutionCache = null;

    /**
     * Send this message just once to the ticke
     *
     * @param string Code of the message
     * @param string Text of the message
     * @param integer|null How many days before we can ask again, NULL means - never ask again
     * @return boolean The ticket is alredy asked (FALSE) or asked now (TRUE)
     **/
    public function askOnce($code, $text, $lag = null) 
    {
        foreach ($this->changelog->get('comment')->getChanges() as $change) {
            // not from me
            if ($change->author != Model_User::getCurrentUser()->email)
                continue;
                
            // not with a code
            if (!preg_match('/^\{{3}\n#!comment\n([\w\d]{32})\n\}{3}\n/', $change->value, $matches))
                continue;
                
            // invalid code
            if ($matches[1] != md5($code))
                continue;
            $asked = true;
        }
        
        // if we can ask just once - and we already asked - skip
        if (isset($asked) && is_null($lag))
            return false;
        
        $lastDate = $this->changelog->get('comment')->getLastDate();
        // we posted it recently
        if ($lastDate->isEarlier(Zend_Date::now()->subDays($lag))) {
            logg(
                "No '%s' to ticket #%d since we already did it %0.1f hours ago, at %s" .
                $code, 
                $this->_id, 
                Zend_Date::now()->sub($lastDate)->getSeconds(), 
                $lastDate->get(DATETIME_MEDIUM)
            );
            return false;
        }
            
        $this->changelog->set('comment', "{{{\n#!comment\n" . md5($code) . "\n}}}\n" . $text);
        return true;
    }

    /**
     * Issue really exists in tracker?
     *
     * @return integer ID of this issue in Trac
     **/
    public function exists() 
    {
        // if TRAC id already exists in the class - we are sure that issue exists in trac
        if ($this->_id)
            return true;
        
        // if we already checked this issue and know that it's absent
        if ($this->_id === false)
            return false;
        
        // get list of IDs with this code (we expect JUST ONE)
        $ids = $this->_tracker->getXmlProxy()->query("code='" . Model_Pages_Encoder::encode($this->code) . "'");
        
        // nothing or something strange
        if (count($ids) != 1) {
            logg("Trac by code '%s' returned %d tickets", $this->code, count($ids));
            return $this->_id = false;
        }
    
        // remember found ID in the class and return it
        $this->_id = array_pop($ids);
        logg("Issue #%d found in Trac for code '%s'", $this->_id, $this->code);
        return $this->_id;
    }
        
    /**
     * Load changelog data
     *
     * @return void
     **/
    protected function _loadChangelog() 
    {
        $log = $this->_tracker->getXmlProxy()->changeLog($this->_id);
        // logg("Issue #%d has %d changes in Trac", $this->_id, count($log));
        
        $fields = array();
        $records = array();
        foreach ($log as $record) {
            $records[] = array(
                $record[2], // name of field
                $record[4], // value
                $record[1], // author
                $record[0] // date of change
            );
            $fields[$record[2]] = true;
        }

        $details = $this->_tracker->getXmlProxy()->get($this->_id);
        
        foreach ($details[3] as $k=>$v) {
            if (!isset($fields[$k]))
                $records[] = array($k, $v, $details[3]['reporter'], $details[1]);
        }

        foreach ($records as $record) {
            list($name, $value, $author, $date) = $this->_translateFromTrac(
                $record[0], // name of field
                $record[1], // value of this field
                $record[2], // author of changes
                $record[3] // date of change
            );
                
            if (!$this->_changelog->allowsField($name)) {
                continue;
            }
                
            $this->_changelog->load(
                $name, 
                $value, 
                $author, 
                $date
            );
        }
    }
        
    /**
     * Save all changes made in changelog and create issue before, if necessary
     *
     * @return void
     **/
    protected function _saveChangelog() 
    {
        $pairs = $this->_changelog->whatToSave();
        
        // nothing was changed?
        if (!count($pairs))
            return;
        
        // maybe it's alive already?
        if (!$this->exists()) {
            // make sure it has the right code
            $pairs['code'] = $this->code;

            // create new ticket in trac
            $this->_id = $this->_tracker->getXmlProxy()->create(
                (string)$this->_changelog->get('summary')->getValue(),
                (string)$this->_changelog->get('description')->getValue(),
                $this->_translateToTrac($pairs),
                true);
            
            logg('Trac ticket #%d was created', $this->_id);
        } else {
            $pairs['action'] = 'leave';
            $this->_tracker->getXmlProxy()->update(
                $this->_id, 
                isset($pairs['comment']) ? $pairs['comment'] : '',
                $this->_translateToTrac($pairs),
                true);
            logg('Trac ticket #%d was updated', $this->_id);
        }
    }

    /**
     * Translate what we got from Trac to our changelog-suitable pair
     *
     * @param string Name of the field
     * @param string Value of the field
     * @param string Author of the field
     * @param string Date of change of the field
     * @return array
     * @throws Model_Asset_Defects_Issue_Trac_UnknownField
     **/
    protected function _translateFromTrac($name, $value, $author, $date) 
    {
        switch ($name) {
            case 'code':
                $value = Model_Pages_Encoder::decode($value);
                break;
            
            // we ignore them
            case 'cc':
            case 'keywords':
            case 'milestone':
            case 'attachment':
            case 'version':
                $name = null;
                break;
                
            case 'resolution':
                switch ($value) {
                    case 'fixed':
                        $this->_resolutionCache = Model_Asset_Defects_Issue_Changelog_Field_Status::FIXED;
                        break;
                    default: 
                        $this->_resolutionCache = Model_Asset_Defects_Issue_Changelog_Field_Status::INVALID;
                        break;
                }
                break;

            case 'status':
                switch ($value) {
                    case 'closed':
                        if (!is_null($this->_resolutionCache))
                            $value = $this->_resolutionCache;
                        else
                            $value = Model_Asset_Defects_Issue_Changelog_Field_Status::INVALID;
                        break;
                    default: 
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Status::OPEN;
                        break;
                }
                break;
        
            // type of ticket
            case 'type':
                switch ($value) {
                    case 'task':
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Type::TASK;
                        break;
                    default: 
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Type::DEFECT;
                        break;
                }
                break;

            // priority of ticket
            case 'priority':
                switch ($value) {
                    case 'minor':
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Priority::MINOR;
                        break;
                    case 'critical':
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Priority::CRITICAL;
                        break;
                    case 'blocker':
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Priority::BLOCKER;
                        break;
                    default: 
                        $value = Model_Asset_Defects_Issue_Changelog_Field_Priority::MAJOR;
                        break;
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
                // hm... somehow we should convert text to days
                // $value = array_search($value, Shared_Trac::getDurationOptions());
                // if ($value === false)
                    $value = 20; // 20 days if Trac value is not clear
                break;

            default:
                FaZend_Exception::raise(
                    'Model_Asset_Defects_Issue_Trac_UnknownField',
                    "Unknown field came from Trac: '{$name}', value: '{$value}'"
                );
        }
        
        return array(
            (string)$name, 
            $value, 
            (string)$author, // author
            new Zend_Date(strtotime($date)) // moment of change
        );
    }
    
    /**
     * Convert our internal fields to Trac
     *
     * @param array List of pairs to be sent to trac
     * @return array
     * @throws Model_Asset_Defects_Issue_Trac_UnknownField
     **/
    protected function _translateToTrac(array $pairs) 
    {
        foreach ($pairs as $name=>&$value) {
            switch ($name) {

                case 'code':
                    $value = Model_Pages_Encoder::encode($value);
                    break;

                case 'type':
                    switch ($value) {
                        case Model_Asset_Defects_Issue_Changelog_Field_Type::TASK:
                            $value = 'task';
                            break;
                        default: 
                            $value = 'defect';
                            break;
                    }
                    break;

                case 'status':
                    switch ($value) {
                        case Model_Asset_Defects_Issue_Changelog_Field_Status::OPEN:
                            $value = 'reopened'; // we open it again
                            $pairs['resolution'] = ''; // delete the resolution
                            break;
                        case Model_Asset_Defects_Issue_Changelog_Field_Status::INVALID:
                            $pairs['resolution'] = 'invalid';
                            $value = 'closed';
                            break;
                        case Model_Asset_Defects_Issue_Changelog_Field_Status::FIXED:
                            $pairs['resolution'] = 'fixed';
                            $value = 'closed';
                            break;
                        default:
                            break;
                    }
                    break;


                case 'priority':
                    switch ($value) {
                        case Model_Asset_Defects_Issue_Changelog_Field_Priority::MINOR:
                            $value = 'minor';
                            break;
                        case Model_Asset_Defects_Issue_Changelog_Field_Priority::CRITICAL:
                            $value = 'critical';
                            break;
                        case Model_Asset_Defects_Issue_Changelog_Field_Priority::BLOCKER:
                            $value = 'blocker';
                            break;
                        default: 
                            $value = 'major';
                            break;
                    }
                    break;
                    
                case 'duration':
                    // $options = Shared_Trac::getDurationOptions();
                    // if (isset($options[$value]))
                    //     $value = $options[$value];
                    // else
                    //     $value = array_pop($options);
                    $value = '2-3 days';
                    break;

                case 'summary':
                case 'description':
                case 'comment':
                    unset($pairs[$name]);
                    break;
                    
                case 'component':
                case 'owner':
                case 'reporter':
                case 'cost':
                case 'action':
                    break;

                default:
                    FaZend_Exception::raise(
                        'Model_Asset_Defects_Issue_Trac_UnknownField',
                        "Unknown field going into Trac: '{$name}', value: '{$value}'"
                    );

            }
        }
        
        return $pairs;
    }
        
}
