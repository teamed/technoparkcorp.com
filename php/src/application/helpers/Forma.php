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
class Helper_Forma extends FaZend_View_Helper {

    /**
     * Fields
     *
     * @var Model_Form_Field[]
     */
    protected $_fields;

    /**
     * Builds the object
     *
     * @return Helper_Forma
     */
    public function forma() {
        $this->getView()->includeCSS('helper/forma.css');
        return $this;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function __toString() {
        return (string)$this->_render();
    }

    /**
     * Add new field
     *
     * @param string Name of field class
     * @param string|null Name of the field to create
     * @return Helper_Forma
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
        $form = new FaZend_Form();

        $form->setView($this->getView())
            ->setMethod('post')
            ->setDecorators(array())
            ->addDecorator('FormElements')
            ->addDecorator('Form');

        foreach ($this->_fields as $name=>$field) {
            $form->addElement($field->getFormElement($name));
        }

        $log = '';
        if (!$form->isFilled() || !$this->_process($form, $log))
            return '<p>' . (string)$form->__toString() . '</p>' .
                ($log ? '<pre class="log">' . $log . '</pre>' : false);

        $this->getView()->formaCompleted = $log;
        return '';
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
                FaZend_Exception::raise('Helper_Forma_FieldAlreadyExists', "Field '{$name}' already exists in the form");
            return $name;
        }

        $newId = 1;
        foreach ($this->_fields as $id=>$field) {
            if (preg_match('/^field(\d+)$/', $id, $matches))
                $newId = (int)$matches[1] + 1;
        }

        return 'field' . $newId;
    }

    /**
     * Process the form and execute what is required
     *
     * @param Zend_Form The form
     * @param string Log to save
     * @return boolean Processed without errors?
     */
    protected function _process(Zend_Form $form, &$log) {

        FaZend_Log::getInstance()->addWriter('Memory', 'forma');

        // HTTP POST request holder
        $request = Zend_Controller_Front::getInstance()->getRequest();

        // find the clicked button
        foreach ($form->getElements() as $element) {
            if (!$element instanceof Zend_Form_Element_Submit)
                continue;

            // whether this particular form was submitted by this button?
            if ($element->getLabel() == $request->getPost($element->getName())) {
                $submit = $element;
                break;
            }
        }

        // get callback params from the clicked button
        list($class, $method) = $this->_fields[$submit->getName()]->action;

        // prepare method calling params for this button/callback
        $rMethod = new ReflectionMethod($class, $method);
        $methodArgs = $mnemos = array();

        try {

            foreach ($rMethod->getParameters() as $param) {
                $methodArgs[$param->name] = $this->_getPostParam($param);
                $mnemos[] = (is_scalar($methodArgs[$param->name]) ? $methodArgs[$param->name] : get_class($methodArgs[$param->name]));
            }

            FaZend_Log::info('Calling ' . $rMethod->getDeclaringClass()->name . '::' . $method .
                '(\'' . implode("', '", $mnemos) . '\')');

            // execute the target method
            call_user_func_array(array($class, $method), $methodArgs);
            
            $result = true;

        } catch (Exception $e) {

            $submit->addError($e->getMessage());

            $result = false;

        }

        $log = FaZend_Log::getInstance()->getWriter('forma')->getLog();
        FaZend_Log::getInstance()->removeWriter('forma');

        return $result;

    }

    /**
     * Get param from POST
     *
     * Retrieve param using POST data and form configuration
     *
     * @return class
     * @throws Helper_Forma_ParamNotFound
     */
    protected function _getPostParam(ReflectionParameter $param) {
        // HTTP POST request holder
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $post = $request->getPost($param->name);
        if (!$post) {
            if ($param->isOptional())
                return $param->getDefaultValue();
            else
                FaZend_Exception::raise('Helper_Forma_ParamNotFound',
                    "Field '{$param->name}' not found in forma, but is required by action");
        }

        return $this->_fields[$param->name]->getMethodParam($param);

    }

}
