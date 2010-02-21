<?php
/**
 * @version $Id$
 */

class Mocks_Shared_Trac extends Shared_Trac
{

    public static function get() 
    {
        return new self(Mocks_Shared_Project::get());
    }

}
