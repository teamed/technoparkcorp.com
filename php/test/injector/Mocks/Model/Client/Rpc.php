<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One mock for all client calls (to Trac, Wiki and Pan)
 *
 * @package injector
 */
class mock_Model_Client_Rpc {

    /**
     * Get proxy
     *
     * @return object
     **/
    public function getProxy($name) {
        return $this;
    }

    /**
     * Get full list of wiki pages
     *
     * @return array
     **/
    public function getAllPages() {
        $pages = array();
        foreach (scandir(dirname(__FILE__) . '/wiki') as $file) {
            if ($file[0] == '.')
                continue;
            $pages[] = pathinfo($file, PATHINFO_FILENAME);
        }
        return $pages;
    }

    /**
     * Get wiki page in HTML
     *
     * @param sting Name of the page
     * @return string
     **/
    public function getPageHTML($name) {
        return file_get_contents(dirname(__FILE__) . '/wiki/' . $name . '.html');
    }

}
