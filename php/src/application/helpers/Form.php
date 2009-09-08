<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Form to show
 *
 * @package helpers
 */
class Helper_Form extends FaZend_View_Helper {

    /**
     * Fields
     *
     * @var Model_Form_Field[]
     */
    protected $_fields;

    /**
     * Builds the object
     *
     * @return Helper_Form
     */
    public function form() {
        return $this;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function __toString() {
        return $this->_render();
    }

    /**
     * Add new field
     *
     * @param string Name of field class
     * @param string|null Name of the field to create
     * @return Helper_Form
     */
    public function addField($type, $name = null) {
        $field = Model_Form_Field::factory($type, $this);
        $this->_fields[$this->_uniqueName($name)] = $field;
        return $field;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function _render() {
        $form = new Zend_Form();

        $form->setView($this->getView());
        $form->setMethod('post');

        foreach ($this->_fields as $name=>$field) {
            $form->addElement($field->getFormElement($name));
        }

        return (string)$form->__toString();
    }

    /**
     * Create unique name
     *
     * @param string Name
     * @return string Name which is unique
     */
    protected function _uniqueName($name) {
        if (!is_null($name)) {
            if (isset($this->_fields[$name]))
                FaZend_Exception::raise('Helper_Form_FieldAlreadyExists', "Field '{$name}' already exists in the form");
            return $name;
        }

        $newId = 1;
        $matches = array();
        foreach ($this->_fields as $id=>$field) {
            if (preg_match('/^field(\d+)$/', $id, $matches))
                $newId = (int)$matches[1] + 1;
        }

        return 'field' . $newId;
    }

}
