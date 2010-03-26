<?php
/**
 * @version $Id: PhpinfoTest.php 856 2010-03-25 08:59:11Z yegor256@yahoo.com $
 */

class WebFrontTest extends PhpRack_Test
{

    public function testWebFront()
    {
        $this->_setAjaxOptions(
            array(
                'reload' => 5, // every 5 seconds, if possible
            )
        );
        $this->assert->network->url
            ->url('http://www.tpc2.com')
            ->regex('TechnoPark');
    }

}