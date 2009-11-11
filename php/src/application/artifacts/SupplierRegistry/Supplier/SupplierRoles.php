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
 * List of roles for a supplier
 *
 * @package Artifacts
 */
class theSupplierRoles extends ArrayIterator {

    /**
     * Show it as a string
     *
     * @return void
     **/
    public function __toString() {
        $roles = array();
        foreach ($this as $role)
            $roles[] = strval($role);
        return implode('; ', $roles);
    }

    /**
     * This supplier has this given role?
     *
     * @param string Role
     * @return boolean
     **/
    public function hasRole($role) {
        foreach ($this as $exists)
            if ($exists->name == $role)
                return true;
        return false;
    }

}
