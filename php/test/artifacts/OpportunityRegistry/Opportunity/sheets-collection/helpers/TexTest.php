<?php
/**
 * @version $Id$
 */

require_once 'AbstractTest.php';
require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/helpers/Tex.php';

class Sheet_Helper_TexTest extends AbstractTest
{
    
    public static function providerLatex()
    {
        return array(
            array('', ''),
            array('Silver & Co.', 'Silver \\& Co.'),
            array('quotes "work"', "quotes ``work''"),
            array('it is <b>bold</b>', 'it is \\textbf{bold}'),
            array('it is <i>"italic"</i>', "it is \\textit{``italic''}"),
        );
    }
    
    /**
     * @dataProvider providerLatex
     */
    public function testConvertionsWorkProperly($html, $latex)
    {
        $helper = new Sheet_Helper_Tex();
        $this->assertEquals($latex, $helper->tex($html));
    }

}