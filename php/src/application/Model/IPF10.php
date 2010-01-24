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
    
    const IPF10_LOGIN = 'robot@technoparkcorp.com';
    const IPF10_PASSWORD = 'justatestpassword';
    const IPF10_SECRET = 'secret23';
    
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
            self::IPF10_LOGIN, 
            self::IPF10_PASSWORD, 
            self::IPF10_SECRET);

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

            FaZend_Log::err('Error in IPF10/TikzImage: ' . $e->getMessage());
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

            FaZend_Log::err('Error in IPF10/TikzPDF: ' . $e->getMessage());
            FaZend_Exception::raise('Model_IPF10_TikzPDFSoapFault',
                'SOAP error: '.$e->getMessage());

        }    

    }

    
}

