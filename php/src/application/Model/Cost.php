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
 * Cost/money value holder
 *
 * Use it like this:
 *
 * <code>
 * $cost = new Model_Cost('23 EUR');
 * if ($cost->usd > 50.56) doSmth();
 * $cost->set('18 GBP'); // will do everything automatically
 * </code>
 *
 * @package Model
 */
final class Model_Cost {

    /**
     * The value, in cents
     *
     * @var integer
     */
    protected $_value;
    
    /**
     * Currency
     *
     * @var Zend_Currency
     */
    protected $_currency;

    /**
     * Constructor
     *
     * @param string Text representation of the cost
     * @return void
     */
    public function __construct($value) {
        validate()->numeric($value, "Numeric only so far");
        
        $this->_value = (int)($value * 100);
        // we should implement it properly
        $this->_currency = Model_Flyweight::factory('Zend_Currency', 'en_US', 'USD')
            ->setFormat(array(
                'precision' => 0, // no cents to show
                'display' => Zend_Currency::USE_SYMBOL,
                'position' => Zend_Currency::RIGHT));
    }

    /**
     * Show this value as a string
     *
     * @return string
     **/
    public function __toString() {
        return $this->_currency->toCurrency($this->usd);
    }

    /**
     * Getter dispatcher
     *
     * @return mixed
     **/
    public function __get($name) {
        switch ($name) {
            case 'usd':
                return $this->_getInUsd() / 100;
            case 'cents':
                return $this->_getInUsd();
        }
    }

    /**
     * Return the value in USD (cents)
     *
     * @return integer
     * @todo implement it properly, getting conversion rates somewhere
     **/
    protected function _getInUsd() {
        return $this->_value;
    }

}
