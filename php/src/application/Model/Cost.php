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
final class Model_Cost
{

    /**
     * The value, in cents, in original currency (NOT in USD!)
     *
     * @var integer
     */
    protected $_cents;
    
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
    public function __construct($value = false)
    {
        $this->set($value);
    }

    /**
     * Create class
     *
     * @return Model_Cost
     */
    public static function factory($value)
    {
        return new Model_Cost($value);
    }

    /**
     * Set value
     *
     * @param string Text representation of the cost
     * @return void
     */
    public function set($value)
    {
        $currency = 'USD';
        $value = (string)$value;
        
        if ($value && !is_numeric($value)) {
            if (!preg_match('/^([\-\+]?\d+(?:\.\d+)?)(?:\s?(\w{3}))?$/', str_replace(',', '', $value), $matches))
                FaZend_Exception::raise(
                    'Model_Cost_InvalidFormat', 
                    "Invalid cost format: '{$value}'"
                    );
            $value = $matches[1];
            $currency = $matches[2];
        }
        
        // we should implement it properly
        $this->_currency = FaZend_Flyweight::factory('Zend_Currency', 'en_US', $currency)
            ->setFormat(array(
                'precision' => 2, // cents to show
                'display' => Zend_Currency::USE_SHORTNAME,
                'position' => Zend_Currency::RIGHT));
        $this->_cents = (int)($value * 100);
    }

    /**
     * Show this value as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_currency->toCurrency($this->_cents / 100);
    }

    /**
     * Getter dispatcher
     *
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'usd':
                return $this->_getCents() / 100;
            case 'cents':
                return $this->_getCents();
        }
    }

    /**
     * Return the value in USD (cents)
     *
     * @return integer
     * @todo implement it properly, getting conversion rates somewhere
     */
    protected function _getCents()
    {
        return $this->_cents * $this->_getRate($this->_currency);
    }
    
    /**
     * Add new value to current one
     *
     * @param Model_Cost The cost to add
     * @return $this
     */
    public function add(Model_Cost $cost)
    {
        $this->_cents += $cost->cents;
        return $this;
    }

    /**
     * Deduct this value from current one
     *
     * @param Model_Cost The cost to deduct
     * @return $this
     */
    public function deduct(Model_Cost $cost)
    {
        $this->_cents -= $cost->cents;
        return $this;
    }

    /**
     * Multiply current value by this new value
     *
     * @param integer Multiplier
     * @return $this
     */
    public function multiply($num)
    {
        $this->_cents *= $num;
        return $this;
    }

    /**
     * Divide it
     *
     * @param float|Model_Cost Divider
     * @return $this|float
     */
    public function divide($div)
    {
        if ($div instanceof Model_Cost)
            return $this->_cents / $div->cents;
        $this->_cents /= $div;
        return $this;
    }

    /**
     * Greater than
     *
     * @param Model_Cost|mixed Another value
     * @return boolean
     */
    public function greaterThan(Model_Cost $cost = null, $orEqual = false)
    {
        if (is_null($cost))
            return $this->_cents > 0;
        if ($orEqual)
            return $this->_cents >= $cost->cents;
        return $this->_cents > $cost->cents;
    }

    /**
     * Less than
     *
     * @param Model_Cost|mixed Another value
     * @return boolean
     */
    public function lessThan(Model_Cost $cost = null, $orEqual = false)
    {
        if (is_null($cost))
            return $this->_cents < 0;
        if ($orEqual)
            return $this->_cents <= $cost->cents;
        return $this->_cents < $cost->cents;
    }
    
    /**
     * Get conversion rate for the given currency
     *
     * @param Zend_Currency Currency to work with
     * @return float
     * @todo Implement through www.foxrate.org
     */
    protected function _getRate(Zend_Currency $currency)
    {
        $symbol = $currency->getShortName();
        switch ($symbol) {
            case 'USD':
                return 1;
            case 'EUR':
                return 1.48;
            case 'GBP':
                return 1.9;
        }
        FaZend_Exception::raise('Model_Cost_UnknownCurrency',
            "Unknown currency symbol: '{$symbol}'");
    }

}
