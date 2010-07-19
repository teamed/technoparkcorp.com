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
 * @author Yegor Bugayenko <egor@tpc2.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2008
 * @version $Id: TikzController.php 837 2010-03-16 14:37:24Z yegor256@yahoo.com $
 */

/**
 * Dynamic TIKZ images processing
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class TikzController extends FaZend_Controller_Action
{

    /**
     * Show tikz image
     * 
     * @return void
     */
    public function indexAction()
    {
        return $this->_returnPNG(Model_XML::tikzShow($this->_getParam('tikz')), false);
    }    
    
    /**
     * Clear the DB of tikz images
     * 
     * @return void
     */
    public function cleanAction()
    {
        Model_XML::tikzClean();
        $this->_redirectFlash('TIKZ database cleaned', 'index', 'static');
    }    

}

