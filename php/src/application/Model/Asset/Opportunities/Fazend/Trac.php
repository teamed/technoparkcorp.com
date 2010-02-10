<?php
/**
 * thePanel v2.0, Project Management Software Toolkit
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are PROHIBITED without prior written permission from 
 * the author. This product may NOT be used anywhere and on any computer 
 * except the server platform of TechnoPark Corp. located at 
 * www.technoparkcorp.com. If you received this code occasionally and 
 * without intent to use it, please report this incident to the author 
 * by email: privacy@technoparkcorp.com or by mail: 
 * 568 Ninth Street South 202, Naples, Florida 34102, USA
 * tel. +1 (239) 935 5429
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id: Trac.php 611 2010-02-07 07:43:45Z yegor256@yahoo.com $
 *
 */

/**
 * Database of Opportunities in trac Sales
 *
 * @package Model
 */
class Model_Asset_Opportunities_Fazend_Trac extends Model_Asset_Opportunities_Abstract
{
    
    const PREFIX = 'opp-';
    
    /**
     * Instance of Shared_Wiki
     *
     * @var Shared_Wiki
     **/
    protected $_wiki;
    
    /**
     * Initializer
     *
     * @return void
     **/
    protected function _init() 
    {
        parent::_init();
        $this->_wiki = new Shared_Wiki($this->_project);
    }
    
    /**
     * Get full list of Opportunities (IDs)
     *
     * @return string[]
     **/
    public function retrieveAll() 
    {
        $list = array();
        foreach (preg_grep('/^' . preg_quote(self::PREFIX, '/'). '/', $this->_wiki->getListOfPages()) as $name) {
            $list[] = substr($name, 4);
        }
        return $list;
    }
    
    /**
     * Get full details of Opportunity by ID
     *
     * @param string ID of the Opportunity
     * @param theOpportunity Object to fill with data
     * @return mixed
     **/
    public function deriveById($id, theOpportunity $opportunity) 
    {
        $xml =
        '<?xml version="1.0" encoding="UTF-8" ?><html>' .
        $this->_wiki->getPageContent(self::PREFIX . $id) .
        '</html>';
        
        $nodes = new SimpleXmlIterator($xml);
        $nodes->rewind();
        while ($section = $nodes->current()) {
            $nodes->next();
            // wait for section
            if (strtolower($section->getName()) != 'h1') {
                continue;
            }
            
            // get the name of the section
            $section = str_replace(' ', '', strval($section));
            
            // it's section now, but ignore it if the name is wrong
            if (!Sheet_Abstract::isValidName($section)) {
                continue;
            }
            
            // new instance of configuration
            $config = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><data></data>');
            
            while ($node = $nodes->current()) {
                // wait for paragraph
                if (strtolower($node->getName()) !== 'p') {
                    break;
                }
                
                $nodes->next();

                $node = trim(strval($node), "\r\t\n ");
                if (strpos($node, ':') === false) {
                    continue;
                }
                
                list($name, $value) = explode(':', $node);
                $item = $config->addChild('item', '');
                $item->addAttribute('name', $name);
                $item->addAttribute('value', $value);
                
                while ($ul = $nodes->current()) {
                    // wait for UL
                    if (strtolower($ul->getName()) !== 'ul') {
                        break;
                    }

                    $nodes->next();
                    foreach ($ul->xpath('li') as $li) {
                        $li = trim(strval($li), "\n\t\r ");
                        $subItem = $item->addChild('item', '');
                        if (strpos($li, ':') === false) {
                            $subItem->addAttribute('value', $li);
                        } else {
                            list($n, $v) = explode(':', $li);
                            $subItem->addAttribute('name', $n);
                            $subItem->addAttribute('value', $v);
                        }
                    }
                }
            }
            
            $sheet = Sheet_Abstract::factory($section, $config);
            $opportunity->sheets[] = $sheet;

        }
    }
    
}
