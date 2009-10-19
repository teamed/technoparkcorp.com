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
 * Shortcut for a shared document
 *
 * @package Model
 */
class Model_Shortcut extends FaZend_Db_Table_ActiveRow_shortcut {

    /**
     * Create new link
     *
     * @param Model_User User to get an access to the document
     * @param string Document to see
     * @param boolean Shall we clean all previous links to this document?
     * @return Model_Shortcut
     **/
    public static function create($user, $document, $clean) {
        
        validate()
            ->emailAddress($user, array())
            ->type($document, 'string')
            ->type($clean, 'boolean');

        if ($clean) {
            $sql = self::retrieve()->table()->getAdapter()->quoteInto('document = ?', $document);
            self::retrieve()->table()
                ->delete($sql);
        }
                
        $shortcut = new Model_Shortcut();
        $shortcut->user = $user;
        $shortcut->document = $document;
        $shortcut->save();
        
        return $shortcut;
        
    }
    
    /**
     * Find by hash
     *
     * @param string Hash
     * @return Model_Shortcut
     **/
    public static function findByHash($hash) {
        return self::retrieve()
            ->where('id = ?', $hash)
            ->setRowClass('Model_Shortcut')
            ->fetchRow();
    }
    
    /**
     * Get hash of the object
     *
     * @return string
     **/
    public function getHash() {
        return strval($this);
    }

}
