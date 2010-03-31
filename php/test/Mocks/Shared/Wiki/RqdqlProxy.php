<?php
/**
 * @version $Id$
 */

class Mocks_Shared_Wiki_RqdqlProxy
{

    public function query($query) 
    {
        return simplexml_load_file(dirname(__FILE__) . '/RqdqlProxy/model.xml');
    }

}
