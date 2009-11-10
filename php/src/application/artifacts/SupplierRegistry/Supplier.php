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
 * One supplier
 *
 * @package Artifacts
 */
class theSupplier extends Model_Artifact {

    /**
     * Create new supplier
     *
     * @param string Email of the supplier
     * @param string Full name of he/she
     * @param string ISO-3166 two-letter country code
     * @param string USP
     * @return theSupplier
     **/
    public static function factory($email, $name, $country, $usp) {
        validate()
            ->emailAddress($email, array(), "Invalid format of supplier's email")
            ->false(empty($name), "Name of supplier may not be empty")
            ->countryCode($country, "Invalid country code, two-letters ISO 3166 required");
        
        $supplier = new theSupplier();
        $supplier->email = $email;
        $supplier->name = $name;
        $supplier->country = $country;
        $supplier->save();
        return $supplier;
    }

}
