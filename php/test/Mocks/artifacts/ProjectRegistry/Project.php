<?php

require_once 'artifacts/ProjectRegistry/Project.php';

class Mocks_theProject extends theProject 
{
    
    public static function get()
    {
        return Model_Artifact::root()->projectRegistry[Mocks_Model_Project::NAME];
    }

    public function fzProject() 
    {
        return Mocks_Model_Project::get();
    }
    
}
