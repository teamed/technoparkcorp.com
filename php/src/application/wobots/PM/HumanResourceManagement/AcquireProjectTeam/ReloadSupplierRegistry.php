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
 * Reload supplier registry
 *
 * This decision will reload supplier registry, to make it
 * current. It should be performed regularly to make sure PMO
 * has enough information about suppliers.
 *
 * @package wobots
 */
class ReloadSupplierRegistry extends Model_Decision_PM
{

    /**
     * Reload it
     *
     * @return string|false
     */
    protected function _make()
    {
        $supplierRegistry = Model_Artifact::root()->supplierRegistry;
        
        logg('There are ' . count($supplierRegistry) . ' suppliers in the registry now');
        
        // reload it
        $supplierRegistry->reload();
        
        foreach ($supplierRegistry as $supplier)
            logg("Supplier {$supplier->email} found: {$supplier->rate}");
        
        logg('There are ' . count($supplierRegistry) . ' suppliers in the registry, after reloading');

        return 'Registry reloaded, now it has ' . count($supplierRegistry) . ' suppliers';
    }
    
}
