<?php

class Mocks_theProject extends theProject 
{

    public function fzProject() 
    {
        return Mocks_Model_Project::getInstance();
    }
    
}
