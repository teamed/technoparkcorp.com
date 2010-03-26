<?php
/**
 * @version $Id$
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