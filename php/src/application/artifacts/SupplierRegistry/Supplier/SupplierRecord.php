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
 * One record for a supplier
 *
 * @package Artifacts
 */
class theSupplierRecord extends FaZend_Db_Table_ActiveRow_record implements Model_Artifact_Interface {

    const STATIC_FILES_DIR = '~/records';

    /**
     * Create new record
     *
     * @param theSupplier Owner of the record
     * @param string Text of the record
     * @param string Absolute file name
     * @return theSupplierRecord
     **/
    public static function create(theSupplier $supplier, $text, $file = null) {
        validate()
            ->true(is_null($file) || file_exists($file), "File '{$file}' is absent");
        
        $record = new theSupplierRecord();
        $record->supplier = $supplier;
        $record->text = $text;
        $record->author = Model_User::getCurrentUser()->email;

        if (!is_null($file)) {
            if (APPLICATION_ENV == 'production') {
                $suffix = '/' . $supplier->email . '/' . basename($file);
                $newPath = self::STATIC_FILES_DIR . $suffix;
                copy($file, $newPath);
                $record->file = $suffix;
            } else {
                $record->file = $file;
            }
        }
        
        $record->save();
        return $record;
    }

    /**
     * Return all records for the given supplier
     *
     * @param theSupplier Owner of the record
     * @return theSupplierRecord[]
     */
    public static function retrieveBySupplier(theSupplier $supplier) {
        return self::retrieve()
            ->where('supplier = ?', (string)$supplier)
            ->order('created DESC')
            ->setRowClass('theSupplierRecord')
            ->fetchAll();
    }

    /**
     * This record has file?
     *
     * @return boolean
     **/
    public function hasFile() {
        return !empty($this->file);
    }

}
