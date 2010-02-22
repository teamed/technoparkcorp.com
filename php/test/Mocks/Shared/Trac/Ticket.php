<?php
/**
 * @version $Id$
 */

/**
 * @see Model_Asset_Defects_Issue_Trac
 */
class Mocks_Shared_Trac_Ticket extends Shared_Trac_Ticket
{

    protected static $_attributes = array();
    
    protected static $_classNames = array();
    
    protected static $_lastDates = array();

    /**
     * this is what we are getting from Trac
     * @see Zend_Date
     */
    const TRAC_DATE = 'yMMddTHH:m:s';
    
    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public static function get($id, array $attributes = array(), $className = null) 
    {
        if ($id === false) {
            if (is_null($className)) {
                $className = __CLASS__;
            } else {
                $className = __CLASS__ . '_' . $className;
            }
            if (count(self::$_attributes) > 0) {
                $id = max(array_keys(self::$_attributes)) + 1;
            } else {
                $id = 1;
            }
            self::$_attributes[$id] = $attributes;
            self::$_classNames[$id] = $className;
        } else {
            if (!isset(self::$_attributes[$id])) {
                self::$_attributes[$id] = $attributes;
            }
        }
        
        $className = self::$_classNames[$id];
        return new $className(Mocks_Shared_Trac::get(), $id);
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
            1 => $this->_getLastDate()->get(self::TRAC_DATE),
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
                    0 => $this->_getLastDate()->subHour(rand(10, 1000))->get(self::TRAC_DATE),
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
                0 => $this->_getLastDate()->get(self::TRAC_DATE),
                1 => Model_User::me()->email,
                2 => $field,
                3 => false,
                4 => is_array($value) ? array_shift($value) : $value,
            );
        }
        return $changelog;
    }
    
    protected function _getLastDate()
    {
        if (!isset(self::$_lastDates[$this->getId()])) {
            self::$_lastDates[$this->getId()] = Zend_Date::now()->subDay(rand(10, 100));
        }
        return self::$_lastDates[$this->getId()];
    }

}
