<?php

require_once 'AbstractProjectTest.php';

class Model_AssetTest extends AbstractProjectTest 
{

    public function testAllAssetsAreAccessible() 
    {
        $assets = array(
            Model_Project::ASSET_SRS,
            Model_Project::ASSET_DEFECTS,
            Model_Project::ASSET_CODE,
            Model_Project::ASSET_SUPPLIERS,
        );
            
        foreach ($assets as $name) {
            $asset = $this->_project->fzProject()->getAsset($name);
            $this->assertTrue(
                $asset instanceof Model_Asset_Abstract, 
                "Invalid asset by name {$name}"
            );
        }
    }

}