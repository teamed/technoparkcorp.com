<?php
/**
 * @version $Id$
 */

class Mocks_Shared_XmlRpc 
{

    public function getHttpClient() 
    {
        return Mocks_Shared_HttpClient::get();
    }

    public function getProxy($name) 
    {
        return $this;
    }

    public function getAllPages() 
    {
        $pages = array();
        foreach (scandir(dirname(__FILE__) . '/wiki-pages') as $file) {
            if ($file[0] == '.') {
                continue;
            }
            $pages[] = pathinfo($file, PATHINFO_FILENAME);
        }
        return $pages;
    }

    public function getPageHTML($name) 
    {
        $html = file_get_contents(dirname(__FILE__) . '/wiki-pages/' . $name . '.html');
        $html = preg_replace(
            '/\{(.*?)\}/', 
            '<a href="http://trac.fazend.com/' . Mocks_Model_Project::NAME . '/wiki-pages/${1}">${1}</a>', 
            $html
        );
        return $html;
    }
    
    public function query($query) 
    {
        switch (true) {
            // get all tickets about suppliers
            case $query == Model_Asset_Suppliers_Fazend_Trac::QUERY_ALL:
                $list = array();
                for ($i = 0; $i<5; $i++) {
                    $list[] = Mocks_Shared_Trac_Ticket::get(
                        false, 
                        array(
                            'supplier' => "test{$i}@example.com",
                        )
                    );
                }
                break;
                    
            // get tickets about one supplier provided
            case (substr($query, 0, strlen(Model_Asset_Suppliers_Fazend_Trac::QUERY_SINGLE)) ==
                Model_Asset_Suppliers_Fazend_Trac::QUERY_SINGLE):
                
                $skills = array('PHP', 'jQuery', 'XML', 'Java', 'EJB');
                $roles = array('Programmer', 'Tester', 'Architect', 'Designer');
                shuffle($skills);
                
                $list = array(
                    Mocks_Shared_Trac_Ticket::get(
                        false, 
                        array(
                            'supplier' => substr($query, -strlen(Model_Asset_Suppliers_Fazend_Trac::QUERY_SINGLE)),
                            'skills' => implode(', ', array_slice($skills, 0, 3)),
                            'role' => $roles[array_rand($roles)],
                            'price' => rand(8, 20) . ' EUR',
                            'date' => Zend_Date::now()->sub(rand(5, 30), Zend_Date::DAY)->get(Mocks_Shared_Trac_Ticket::TRAC_DATE),
                        )
                    ),
                );
                break;
                
            // full list of tickets in test project
            case preg_match(
                '/^' . preg_quote(Model_Asset_Defects_Fazend_Trac::QUERY_ALL, '/') . '&max=(\d+)&page=(\d+)$/', 
                $query, 
                $matches
            ):
                $list = array();
                foreach (glob(dirname(__FILE__) . '/Trac/Ticket/*.php') as $file) {
                    $list[] = Mocks_Shared_Trac_Ticket::get(false, array(), pathinfo($file, PATHINFO_FILENAME));
                }
                if ($matches[2] < 3) {
                    for ($i = 0; $i<$matches[1]; $i++) {
                        $list[] = Mocks_Shared_Trac_Ticket::get(false, array());
                    }
                }
                break;

            // one ticket by ID
            case preg_match('/^id=(\d+)/', $query, $matches):
                $list = array(
                    Mocks_Shared_Trac_Ticket::get($matches[1], array()),
                );
                break;

            // one ticket by CODE
            case preg_match('/^code=([\w\d\-]+)/', $query):
                $list = array(
                    Mocks_Shared_Trac_Ticket::get(false, array()),
                );
                break;

            // one ticket by any other param
            case preg_match('/^(?:reporter|owner|status|severity|milestone|component|resolution)=(.*?)/', $query):
                $list = array(
                    Mocks_Shared_Trac_Ticket::get(false, array()),
                );
                break;

            default:
                FaZend_Exception::raise(
                    'Mocks_Shared_XmlRpc_NotImplemnetedYet',
                    "We can't return anything for your request: '$query'"
                );
        }
        
        $ids = array();
        foreach ($list as $ticket)
            $ids[] = $ticket->getId();
        return $ids;
    }

    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public function create($summary, $description, $params, $smth) 
    {
        return 1;
    }

    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public function update($id, $summary, $params, $smth) 
    {
        // ...
    }

    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public function changeLog($id) 
    {
        return Mocks_Shared_Trac_Ticket::get($id)->getTracChangelog();
    }

    /**
     * @see Model_Asset_Defects_Issue_Trac
     */
    public function get($id) 
    {
        return Mocks_Shared_Trac_Ticket::get($id)->getTracDetails();
    }
    
    public function getAll() 
    {
        return array('test1', 'test2');
    }
    
}
