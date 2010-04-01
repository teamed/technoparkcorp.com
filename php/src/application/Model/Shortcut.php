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
 * Shortcut for a shared document
 *
 * @package Model
 */
class Model_Shortcut extends FaZend_Db_Table_ActiveRow_shortcut
{

    const PREFIX_LENGTH = 3;
    const PLUS = 3;
    const MULTIPLY = 7;

    /**
     * Create new link
     *
     * @param string Document to see
     * @param array List of emails to get an access to the document
     * @param array Associative array of params
     * @param boolean Shall we clean all previous links to this document?
     * @return Model_Shortcut
     */
    public static function create($document, array $emails, array $params, $clean) 
    {
        validate()
            ->type($document, 'string')
            ->type($clean, 'boolean');

        foreach ($emails as $email)
            validate()->emailAddress($email, array());

        if ($clean) {
            $sql = self::retrieve()->table()->getAdapter()->quoteInto('document = ?', $document);
            self::retrieve()->table()
                ->delete($sql);
        }
                
        $shortcut = new Model_Shortcut();
        $shortcut->emails = serialize($emails);
        $shortcut->document = $document;
        $shortcut->author = Model_User::me()->email;
        $shortcut->params = serialize($params);
        $shortcut->save();
        
        return $shortcut;
    }
    
    /**
     * Get URL of the shortcut
     *
     * @return string
     */
    public function getUrl() 
    {
        return Zend_Registry::getInstance()->view
            ->serverUrl(array('doc'=>$this->getHash()), 'shared', true);
    }
    
    /**
     * Find by hash
     *
     * @param string Hash
     * @return Model_Shortcut
     */
    public static function findByHash($hash) 
    {
        $hash = substr($hash, self::PREFIX_LENGTH) / self::MULTIPLY - self::PLUS;
        return self::retrieve()
            ->where('id = ?', $hash)
            ->setRowClass('Model_Shortcut')
            ->fetchRow();
    }
    
    /**
     * Get hash of the object
     *
     * @return string
     */
    public function getHash() 
    {
        return substr(md5(time()), 0, self::PREFIX_LENGTH) . // just a random prefix
            ((intval(strval($this)) + self::PLUS) * self::MULTIPLY);
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams() 
    {
        return unserialize($this->params);
    }

    /**
     * Get list of emails
     *
     * @return array
     */
    public function getEmails() 
    {
        return unserialize($this->emails);
    }

}
