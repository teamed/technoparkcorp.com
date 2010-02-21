<?php
/**
 * @version $Id$
 */

require_once 'AbstractProjectTest.php';

class IssuesTest extends AbstractProjectTest
{

    public function testListOfIssuesIsAccessible()
    {
        $issues = $this->_project->issues;
        $this->assertTrue($issues instanceof theIssues, 
            "Invalid type of issue holder");

        logg('issues found: %s', implode(', ', array_keys(iterator_to_array($issues))));

        $cnt = count($issues);
        foreach ($issues as $id=>$issue) {
            // logg(spl_object_hash($issue));
            $this->assertTrue($issue instanceof Model_Asset_Defects_Issue_Abstract,
                "Invalid type of issue #{$id}: " . gettype($issue));
            $this->assertEquals($issue->id == $id, "ID's are different");
            $this->assertTrue($issue === $this->_project->fzProject()
                ->getAsset(Model_Project::ASSET_DEFECTS)->findById($id),
                "Issue and Ticket objects are different (ID: $id)");
            $cnt--;
            
            $this->assertTrue($issue->changelog instanceof Model_Asset_Defects_Issue_Changelog_Changelog,
                "Invalid type of changelog: " . gettype($issue->changelog));
                
            foreach (array('status', 'comment', 'summary', 'owner') as $name) {
                $field = $issue->changelog->get($name);
                $this->assertTrue($field instanceof Model_Asset_Defects_Issue_Changelog_Field_Abstract,
                    "Invalid type of field '{$name}': " . gettype($field));
                
                $this->assertNotEquals(false, $field->getValue(),
                    "Failed to retrieve '{$name}': '{$field->getValue()}'");
            }
        }
        
        $this->assertEquals(0, $cnt);
    }

    // public function testRealLifeListOfIssuesIsAccessible() 
    // {
    //     if (!defined('TEST_REAL_CONNECTIONS'))
    //         return $this->markTestIncomplete();
    //         
    //     Shared_Pan::setSoapClient(null);
    //     Mocks_Shared_Soap_Client::setLive();
    //     try {
    //         $issues = $this->_project->issues;
    //         foreach ($issues as $issue) {
    //             $this->assertTrue(is_int($issue->changelog->get('status')->getValue()),
    //                 "STATUS is invalid");
    //             $this->assertTrue(is_bool($issue->isClosed()),  
    //                 "isClosed() is invalid");
    // 
    //             logg(
    //                 'ticket #%s, status: %s, summary: %s',
    //                 $issue->id,
    //                 $issue->changelog->get('status')->getValue(),
    //                 $issue->changelog->get('summary')->getValue()                    
    //             );
    //         }
    //     } catch (Exception $e) {
    //         FaZend_Log::err("Failed to get issues: " . $e->getMessage());
    //         $incomplete = true;
    //     }
    //     
    //     Mocks_Shared_Soap_Client::setTest();
    //     Shared_Pan::setSoapClient(Mocks_Shared_Pan_SoapClient::get());
    //     
    //     if (isset($incomplete))
    //         $this->markTestIncomplete();
    // }

}