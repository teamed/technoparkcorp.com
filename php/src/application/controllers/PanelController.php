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
 * Panel pages
 *
 * @package Controllers
 */
class PanelController extends FaZend_Controller_Action {

    /**
     * Pre-configuration
     *
     * @return void
     */
    public function preDispatch() {

        Zend_Layout::getMvcInstance()->setLayout('panel');

    }

    /**
     * Default and the only action for this controller
     *
     * @return void
     */
    public function indexAction() {

        $view = new Zend_View();
        $doc = $view->doc = $this->_getParam('doc');

        // convert document name into absolute PATH
        $path = Model_Pages::resolvePath($doc);

        // reconfigure VIEW in order to render this particular document file
        $view->addScriptPath(dirname($path));
        $this->view->document = $view->render(pathinfo($path, PATHINFO_BASENAME));

    }

}
