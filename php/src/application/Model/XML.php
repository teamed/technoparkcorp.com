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
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * XML document. Decorator around SimpleXML
 *
 * @package cls
 */
class Model_XML
{

    /**
     * Cache of PNG/TIKZ images
     *
     * @var Zend_Cache
     */
    protected static $_cache;
    
    /**
     * View to render TIKZ/TEX files
     *
     * @var Zend_Cache
     */
    protected static $_view = null;
    
    /**
     * SimpleXML document
     *
     * @var SimpleXML
     */
    protected $_xml;
    
    /**
     * Create an instance of this class
     *
     * @param SimpleXML instance
     * @return void
     */
    protected function __construct($xml)
    {
        $this->_xml = $xml;

        if (!isset(self::$_view)) {
            self::$_view = clone Zend_Registry::getInstance()->view;    
            self::$_view->setScriptPath(APPLICATION_PATH . '/tikz');
            self::$_view->setFilter(null);
        }
    }

    /**
     * Create new class from XML file
     *
     * @return XMLDocument
     */
    public static final function loadFile($fileName)
    {
        return new Model_XML(simplexml_load_file($fileName));
    }

    /**
     * Create new class from XML text
     *
     * @return XMLDocument
     */
    public static final function loadXML($text)
    {
        return new Model_XML(simplexml_load_string($text));
    }

    /**
     * Gateway to SimpleXML
     *
     * @return var
     */
    public function __get($key)
    {
        if (isset($this->_xml->$key))
            return new Model_XML($this->_xml->$key);
        return false;
    }

    /**
     * Gateway to SimpleXML
     *
     * @return var
     */
    public function __set($key, $value)
    {
        $this->_xml->$key = $value;
    }

    /**
     * Gateway to SimpleXML
     *
     * @return var
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_xml, $method), $args);
    }
    
    /**
     * Get list of attributes
     *
     * @return SimpleXMLElement
     */
    public function attributes() 
    {
        return $this->_xml->attributes();
    }

    /**
     * Returns element's value
     *
     * @return string
     */
    public function __toString()
    {
        $xml = $this->_xml->asXML();
        $name = $this->_xml->getName();

        // kill open and closing tags
        //$xml = substr($xml, strlen($name)+2, strlen($xml)-(strlen($name)*2+5));
        $xml = preg_replace(array('/\<'.preg_quote($name).'.*?\>/', '/\<\/'.preg_quote($name).'\>/'), '', $xml);

        try {
            return $this->_parseImages($this->_parseMetas($xml));
        } catch (Exception $e) {
            return get_class($e) . ': ' . $e->getMessage();
        }
    }

    /**
     * Parse the string for meta tags
     *
     * @param string XML text
     * @return string
     */
    protected function _parseImages($text)
    {
        // replace propable HTML special chars
        $text = preg_replace('/\&lt\;(\/?)tikz(\s.*?)?\&gt\;/m', '<${1}tikz${2}>', $text);

        // see this documentation on patterns: http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
        if (!preg_match_all('/\<(tikz|png)(.*)\>(.*)\<\/(tikz|png)\>/ismU', $text, $matches))
            return $text;

        // replace them with proper image callings
        foreach ($matches[3] as $key=>$match) {
            $md5 = md5($match);

            $text = str_replace(
                $matches[0][$key], 
                "<img alt='loading...' src='" .
                self::$_view->url(array('tikz'=>$md5), 'tikz', true) . "' " . $matches[2][$key] . " />", 
                $text
            );

            // what type of image it is?    
            switch ($matches[1][$key]) {    
                
                case 'tikz':

                    // if this image is already in cache, either
                    // in PNG or in TEX form
                    if (self::_cache()->test($md5) || self::_cache()->test($md5 . '_png'))
                        continue;    

                    self::$_view->tikz = $match;

                    // save the file, which will be used later, but HTTP call
                    self::_cache()->save(self::$_view->render('image.tex'), $md5);    

                    break;    

                case 'png':

                    // either it's right inside the tag, or somewhere in XML document
                    if (strlen($match) < 50) {
                        $listOfImages = $this->xpath($match);
                        $png = (string)$listOfImages[0];
                    } else {
                        $png = $match;      
                    }

                    // just save what you see in the <png> tag
                    self::_cache()->save(convert_uudecode(htmlspecialchars_decode(trim($png))), $md5 . '_png');

                    break;    

            }    
        }    
        return $text;
    }

    /**
     * Parse the string for meta tags
     *
     * @param string XML text
     * @return string
     */
    protected function _parseMetas($text)
    {
        if (!preg_match_all('/\${(\w+)\:(.*)}/smU', $text, $matches))
            return $text;

        // replace them with proper image callings
        foreach ($matches[1] as $key=>$match) {

            switch ($match) {

                case 'url':
                    $replacement = self::$_view->staticUrl($matches[2][$key]);
                    break;

                case 'img':
                    $replacement = self::$_view->viewFile($matches[2][$key]);
                    break;

                case 'mailto':
                    $replacement = 'mailto:' . $matches[2][$key] . 
                    '@' . preg_replace('/^https?\:\/\//', '', WEBSITE_URL);
                    break;

            }

            if (isset($replacement)) {
                $text = str_replace($matches[0][$key], $replacement, $text);
                unset($replacement);
            }

        }                                   

        return $text;
    }

    /**
     * Clean the entire DB of tikz images
     *
     * @return void
     */
    public static final function tikzClean()
    {
        self::_cache()->clean();
    }

    /**
     * Show one image, created with LaTeX Tikz
     *
     * @param string MD5 code of the requested image to show
     * @return string
     */
    public static final function tikzShow($md5)
    {
        // if this PNG already exists
        if (self::_cache()->test($md5 . '_png')) 
            return self::_cache()->load($md5 . '_png');

        // even if the source is absent?    
        if (!self::_cache()->test($md5)) {
            FaZend_Log::err('Call made to an absent TIKZ image: ' . $md5);
            return self::_errorPNG($md5, Model_Colors::BLUE);
        }

        // try to get PNG from IPF10
        try {
            // $png = Model_IPF10::getInstance()->TikzImage(self::_cache()->load($md5));
        } catch (Exception $e) {
            return self::_errorPNG($md5, Model_Colors::RED);
        }
        
        // maybe the result returned is empty?
        if (!$png) {
            FaZend_Log::err('Error in XML/tikzShow - empty PNG, will try again next time');
            return self::_errorPNG($md5, Model_Colors::GRAY);
        }
        
        // maybe the PNG is not a valid image?
        if (imagecreatefromstring($png) === false) {
            FaZend_Log::err(
                'Error in XML/tikzShow - invalid PNG (' . strlen($png) . ' bytes), will try again next time'
            );
            return self::_errorPNG($md5, Model_Colors::YELLOW);
        }

        self::_cache()->save($png, $md5 . '_png');
        self::_cache()->remove($md5);

        // return PNG image content
        return $png;
    }

    /**
     * Show error message in PNG
     *
     * @param string MD5 code of the requested (and failed) image
     * @return string
     */
    protected static function _errorPNG($md5, $color = Model_Colors::RED)
    {
        $img = imagecreatetruecolor(50, 30);

        imagefill($img, 0, 0, Model_Colors::getForImage($img, $color));
        imageline($img, 0, 0, 49, 29, Model_Colors::getForImage($img, Model_Colors::WHITE));
        imageline($img, 0, 29, 49, 0, Model_Colors::getForImage($img, Model_Colors::WHITE));
            
        ob_start();
        imagepng($img);
        return ob_get_clean();
    }    

    /**
     * Get an instance of cache
     *
     * @return Zend_Cache
     */
    protected static function _cache()
    {
        if (self::$_cache != false)
            return self::$_cache;

        self::$_cache = Zend_Cache::factory(
            'Core', 
            'File', 
            array(
                'caching' => true,
                'cache_id_prefix' => 'panel2tikz',
                'lifetime' => SECONDS_IN_DAY * 10,
                'automatic_serialization' => true,
                'automatic_cleaning_factor' => false,
                'write_control' => true,
                'logging' => false,
                'ignore_user_abort' => true
            ), 
            array(
                'cache_dir' => sys_get_temp_dir(),
                'hashed_directory_level' => 0,
                'read_control' => false,
                'file_locking' => false,
                'file_name_prefix' => 'tikz'
            )
        );

        return self::$_cache;
    }

}
