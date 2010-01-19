<?php

class Mocks_Shared_Soap_Client
{
    
    public static function get() 
    {
        return new self();
    }
    
    public function getProjects() 
    {
        return array(
            FaZend_StdObject::create()
                ->set('id', 1)
                ->set('name', Mocks_Model_Project::NAME)
                ->set('user', 1)
                ->set('authz', "[" . Mocks_Model_Project::NAME . ":/]\n" . 
                    Mocks_Model_Project::PM . " = rw\n" .
                    Model_Wobot_PM::getEmailByProjectName(Mocks_Model_Project::NAME) . " = rw\n" .
                    "yegor256@yahoo.com = rw\n"
                    )
                ->set('passwd', "[users]\n" . 
                    Mocks_Model_Project::PM . " = " . Mocks_Model_Project::PM_PWD . "\n" .
                    Model_Wobot_PM::getEmailByProjectName(Mocks_Model_Project::NAME) . " = test\n" .
                    "yegor256@yahoo.com = aF41Atlz\n"
                    )
                ->set('tracIni', ''),
            FaZend_StdObject::create()
                ->set('id', 2)
                ->set('name', 'PMO')
                ->set('user', 1)
                ->set('authz', "[PMO:/]\n" . Mocks_Model_Project::PM . " = rw\n")
                ->set('passwd', "[users]\n" . Mocks_Model_Project::PM . " = " . Mocks_Model_Project::PM_PWD . "\n")
                ->set('tracIni', ''),
            );
    }

    public function getUsers() 
    {
        return array(
            FaZend_StdObject::create()
                ->set('id', 1)
                ->set('email', Mocks_Model_Project::PM)
                ->set('password', Mocks_Model_Project::PM_PWD),
            );
    }

}

