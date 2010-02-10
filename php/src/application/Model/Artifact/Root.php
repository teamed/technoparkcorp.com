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
class Model_Artifact_Root extends FaZend_Pos_Root implements Model_Artifact_Interface
{

    /**
     * Initialize it
     *
     * @return void
     */
    public function init() 
    {
        parent::init();

        $artifacts = array(
            'projectRegistry' => new theProjectRegistry(),
            'supplierRegistry' => new theSupplierRegistry(),
            'statements' => new theStatements(),
            'opportunityRegistry' => new theOpportunityRegistry(),
        );

        foreach ($artifacts as $name=>$artifact) {
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
