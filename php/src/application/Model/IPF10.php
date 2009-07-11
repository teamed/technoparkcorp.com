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
 * SOAP client to IPF10 component
 *
 * @package AssetManagement
 */
class Model_IPF10 {

    const WSDL_URI = 'http://ipf10.tpc-svn.com/wsdl.php';
    const COOKIE = 'ipf10session';
    
    /**
     * Singleton pattern
     *
     * @var Model_IPF10
     */
    protected static $_instance;

    /**
     * Instance of SOAP client
     *
     * @var SoapClient
     */
    protected $_soapClient;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct() {
    }

    /**
     * Instance of singleton
     *
     * @return Model_IPF10
     */
    public static function getInstance() {
        if (!isset(self::$_instance))
            self::$_instance = new Model_IPF10();
        return self::$_instance;
    }

    /**
     * Get an instance of SoapClient, and logins automatically
     *
     * @return SoapClient
     */
    protected function _getSoapClient() {

        if (isset($this->_soapClient))
            return $this->_soapClient;

        // connect and configure
        $this->_soapClient = new SoapClient(self::WSDL_URI, array(
            'trace' => true,
            'cache_wsdl' => false,
        ));

        // secret params for login, don't change them
        $id = $this->_soapClient->Login(
            'robot@technoparkcorp.com', 
            'justatestpassword', 
            'secret23');

        // set the name of the cookie
        $this->_soapClient->__setCookie(self::COOKIE, $id);

        // return and save
        return $this->_soapClient;

    }

    /**
     * Convert LaTeX into PNG
     * 
     * @param string LaTeX source
     * @return string
     */
    public function TikzImage($tex) {

        try {

            return convert_uudecode($this->_getSoapClient()->TikzImage((string)$tex));

        } catch (SoapFault $e) {

            FaZend_Exception::raise('Model_IPF10_TikzImageSoapFault',
                'SOAP error: '.$e->getMessage());

        }    

    }

    /**
     * Convert LaTeX into PDF
     * 
     * @param string LaTeX source
     * @return string
     */
    public function TikzPDF($tex) {
    
        try {

            return convert_uudecode($this->_getSoapClient()->TikzPDF((string)$tex));

        } catch (SoapFault $e) {

            FaZend_Exception::raise('Model_IPF10_TikzPDFSoapFault',
                'SOAP error: '.$e->getMessage());

        }    

    }

    
}

