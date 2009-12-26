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
 * Root of all artifacts
 *
 * @package Artifacts
 */
class Model_Artifact_Root extends FaZend_Pos_Root implements Model_Artifact_Interface {

    /**
     * Initialize it
     *
     * @return void
     */
    public function init() 
    {
        parent::init();

        foreach (array(
            'projectRegistry' => new theProjectRegistry(),
            'supplierRegistry' => new theSupplierRegistry(),
            'statements' => new theStatements(),
            ) as $name=>$artifact) {
            // we need this validation for POS
            if (!isset($this->name)) {
                // add them to the root, one by one
                $this->$name = $artifact;
            }
            Model_Artifact::initialize($this, $this->$name, null);
        }
        
        $this->projectRegistry->reload();
    }

}
