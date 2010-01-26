<?php

/**
 * @see Model_Asset_Defects_Issue_Trac
 */
class Mocks_Shared_Trac_Ticket extends Shared_Trac_Ticket
{

    protected static $_attributes = array();

     // this is what we are getting from Trac
    const TRAC_DATE = 'YMMDDTHH:m:s';
    
    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public static function get($id, array $attributes = array()) 
    {
        if ($id === false) {
            if (count(self::$_attributes) > 0)
                $id = max(array_keys(self::$_attributes)) + 1;
            else
                $id = 1;
            self::$_attributes[$id] = $attributes;
        } else {
            if (!isset(self::$_attributes[$id]))
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
    
    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public function getTracDetails() 
    {
        return array(
            0 => false,
            1 => Zend_Date::now()->get(self::TRAC_DATE),
            2 => false,
            3 => $this->getAttributes(),
        );
    }

    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public function getTracChangelog() 
    {
        $data = array(
            'status' => array('open', 'closed', 'invalid'),
            'owner' => array_keys(Mocks_Model_Project::get()->getStakeholders()),
            'summary' => 'to test UC1 and R1', 
            'comment' => 'some testing is required with ActorUser and UC2', 
            'description' => 'it is an initial task spec'
        );
        
        $changelog = array();
        foreach ($data as $field=>$value) {
            for ($i=0; $i<10; $i++) {
                $changelog[] = array(
                    0 => Zend_Date::now()->subHour(rand(1, 100))->get(self::TRAC_DATE),
                    1 => Model_User::me()->email,
                    2 => $field,
                    3 => false,
                    4 => (is_string($value) ? 
                        FaZend_View_Helper_LoremIpsum::getLoremIpsum() :
                        $value[array_rand($value)]
                    ),
                );
            }
            $changelog[] = array(
                0 => Zend_Date::now()->get(self::TRAC_DATE),
                1 => Model_User::me()->email,
                2 => $field,
                3 => false,
                4 => is_array($value) ? array_shift($value) : $value,
            );
        }
        return $changelog;
    }

}
