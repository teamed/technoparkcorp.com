<?php
/**
 * @version $Id: MetricsTest.php 718 2010-02-21 15:55:39Z yegor256@yahoo.com $
 */

require_once 'AbstractTest.php';
require_once 'artifacts/OpportunityRegistry/Opportunity/sheets-collection/helpers/Itemize.php';

class Sheet_Helper_ItemizeTest extends AbstractTest
{
    
    public static function providerLatex()
    {
        return array(
            array(
                'itemize', 
                '\begin{itemize} \item \textbf{programmer} \item architect \item tester \end{itemize}'
            ),
            array(
                'description', 
                '\begin{description} \item[john] \textbf{programmer} \item[peter] architect \item[william] tester \end{description}'
            ),
        );
    }
    
    /**
     * @dataProvider providerLatex
     */
    public function testConvertionsWorkProperly($style, $latex)
    {
        $registry = Model_Artifact::root()->opportunityRegistry;
        // reload it explicitly
        $registry->reload();
        $opp = $registry->current();

        $helper = new Sheet_Helper_Itemize();
        $helper->setView($opp->sheets->getView());

        $list = array(
            'john' => '<b>programmer</b>',
            'peter' => 'architect',
            'william' => 'tester',
        );
        $this->assertEquals(
            $latex, 
            preg_replace('/[\t\n\s]+/', ' ', $helper->itemize($list, $style))
        );
    }

}