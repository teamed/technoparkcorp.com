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
 * One abstract field
 *
 * @package Model_Form
 */
abstract class Model_Form_Field {

    /**
     * Helper instance
     *
     * @var Helper_Form
     */
    protected $_helper;

    /**
     * HTML attributes
     *
     * @var array
     */
    protected $_attribs = array();

    /**
     * Is it required?
     *
     * @var boolean
     */
    protected $_required = true;

    /**
     * Label above the element
     *
     * @var string
     */
    protected $_label;

    /**
     * Help below the element
     *
     * @var string
     */
    protected $_help;

    /**
     * Value validators
     *
     * @var array
     */
    protected $_validators = array();

    /**
     * Factory method
     *
     * @param string Type of field
     * @param Helper_Form Form, the owner
     * @return Model_Form_Field
     * #throws Model_Form_Field_ClassNotFound
     */
    public static function factory($type, Helper_Form $helper) {
        $className = 'Model_Form_Field' . ucfirst($type);
        return new $className($helper);
    }

    /**
     * Private constructor
     *
     * @return void
     */
    protected function __construct(Helper_Form $helper) {
        $this->_helper = $helper;
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    public function getFormElement($name) {
        $element = $this->_getFormElement($name);

        return $element;
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    abstract protected function _getFormElement($name);

    /**
     * Form method gateway
     *
     * @return string
     */
    public function __toString() {
        return $this->_helper->__toString();
    }

    /**
     * Call catcher
     *
     * @param string Method name
     * @param array List of params
     * @return value
     */
    public function __call($method, $args) {
        if (strpos($method, 'field') !== 0)
            return call_user_func_array(array($this->_helper, $method), $args);

        $func = '_set' . substr($method, 5);

        call_user_func_array(array($this, $func), $args);

        return $this;
    }

    /**
     * Setter, to add label to the field
     *
     * @param string Label to show above the field
     * @return void
     */
    protected function _setLabel($label) {
        $this->_label = $label;
    }

    /**
     * Setter, to add help message to the field
     *
     * @param string Help to show below the field
     * @return void
     */
    protected function _setHelp($help) {
        $this->_help = $help;
    }

    /**
     * This field is required
     *
     * @param boolean Is it required?
     * @return void
     */
    protected function _setRequired($required = true) {
        $this->_required = $required;
    }

    /**
     * Set new HTML attribute
     *
     * @param string Attribute name
     * @param string Attribute value
     * @return void
     */
    protected function _setAttrib($attrib, $value) {
        $this->_attribs[$attrib] = $value;
    }

    /**
     * Set new validator
     *
     * @param callback Validator of the field value
     * @return void
     */
    protected function _setValidator($validator) {
        $this->_validators[] = $validator;
    }

}
