<?php

require_once 'AbstractProjectTest.php';

class IssuesTest extends AbstractProjectTest
{

    public function testListOfIssuesIsAccessible()
    {
        $issues = $this->_project->issues;
        $this->assertTrue($issues instanceof theIssues, 
            "Invalid type of issue holder");

        $cnt = count($issues);
        foreach ($issues as $id=>$issue) {
            $this->assertTrue($issue instanceof Model_Asset_Defects_Issue_Abstract,
                "Invalid type of issue #{$id}: " . gettype($issue));
            $cnt--;
            
            $this->assertTrue($issue->changelog instanceof Model_Asset_Defects_Issue_Changelog_Changelog,
                "Invalid type of changelog: " . gettype($issue->changelog));
            $this->assertTrue(is_string($issue->changelog->get('comment')->getValue()),
                "Failed to retrieve 'comment'");
        }
        $this->assertEquals(0, $cnt);
    }

}