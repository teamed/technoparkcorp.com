<?php

class Mocks_Shared_Trac_Ticket extends Shared_Trac_Ticket
{

    protected static $_attributes = array();

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
     * @see Shared_Trac_Ticket
     **/
    public function getAttributes() 
    {
        return self::$_attributes[$this->getId()];
    }
    
    public function getTracDetails() 
    {
        return array(
            0 => false,
            1 => Zend_Date::now()->getIso(),
            2 => false,
            3 => $this->getAttributes(),
        );
    }

    public function getTracChangelog() 
    {
        $changelog = array();
        foreach (array('summary', 'comment', 'description') as $field) {
            $changelog[] = array(
                0 => Zend_Date::now()->getIso(),
                1 => Model_User::me()->email,
                2 => $field,
                3 => false,
                4 => 'some text about R1, UC1, ActorUser and others',
            );
        }
        return $changelog;
    }

}
