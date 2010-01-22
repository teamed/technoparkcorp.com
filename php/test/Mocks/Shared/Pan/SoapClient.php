<?php

class Mocks_Shared_Pan_SoapClient 
{

    public static function get() 
    {
        return new self();
    }

    /**
     * @return array[]
     * @see Model_Asset_Design_Fazend_Linux
     **/
    public function getComponents() 
    {
        return array(
            array(
                'name' => 'System',
                'fullName' => 'FaZend.System',
                'type' => 'package',
                'traces' => array('#1', 'FaZend.System.MyClass'),
            ),
            array(
                'name' => 'MyClass',
                'fullName' => 'FaZend.System.MyClass',
                'type' => 'class',
                'traces' => array('#2', 'FaZend.System'),
            ),
        );
    }    

}
