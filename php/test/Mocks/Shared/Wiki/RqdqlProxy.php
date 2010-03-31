<?php
/**
 * @version $Id$
 */

class Mocks_Shared_Wiki_RqdqlProxy
{

    public function query($query) 
    {
        return file_get_contents(dirname(__FILE__) . 'RqdqlProxy/model.xml');
    }

}
