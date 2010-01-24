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
 * Encoder/decoder of strings for URLs
 *
 *
 * @package Controllers
 */
class Model_Pages_Encoder {

    const SALT = 'x8='; // just a secure suffix

    /**
     * Encode text for URL
     *
     * @param string Value to encode
     * @return string
     */
    public static function encode($text) {
        return wordwrap(trim(base64_encode($text . self::SALT), '='), 8, '-', true);
    }

    /**
     * Decode text from URL
     *
     * @param string Value to decode
     * @return string
     */
    public static function decode($text) {
        $text = base64_decode(str_replace('-', '', $text));
        
        // security check
        if (substr($text, strlen($text) - strlen(self::SALT)) != self::SALT)
            FaZend_Exception::raise('Model_Pages_Encoder_InvalidSalt', "Wrong security salt in '{$text}'");
            
        return substr($text, 0, -strlen(self::SALT));
    }

}
