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
 * One mocked ticket in trac
 *
 * @package Mocks
 */
class Mocks_Shared_Trac_Ticket extends Shared_Trac_Ticket
{

    /**
     * List of mocked attributes
     *
     * @var array[]
     **/
    protected static $_attributes = array();

    /**
     * Get the ticket
     *
     * @return Mocks_Shared_Trac_Ticket
     **/
    public static function get($id, array $attributes = null) 
    {
        if ($id === false) {
            if (count(self::$_attributes) > 0)
                $id = max(array_keys(self::$_attributes)) + 1;
            else
                $id = 1;

            self::$_attributes[$id] = $attributes;
        }
        
        return new self(Mocks_Shared_Trac::get(), $id);
    }
    
    /**
     * Here we override method from Shared_Trac_Ticket
     *
     * @return array
     **/
    public function getAttributes() 
    {
        return self::$_attributes[$this->getId()];
    }

}
