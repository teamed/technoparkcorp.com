<?php
/**
 * @version $Id$
 */

class Mocks_Shared_HttpClient
{

    public static function get() 
    {
        return new self();
    }

    public function getUri() 
    {
        return 'http://...';
    }

}
