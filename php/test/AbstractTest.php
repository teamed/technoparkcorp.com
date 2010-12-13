<?php
/**
 * @version $Id$
 */

/**
 * @see FaZend_Test_TestCase
 */
require_once 'FaZend/Test/TestCase.php';

abstract class AbstractTest extends FaZend_Test_TestCase
{

    public function assertStringEquals($str1, $str2)
    {
        if ($str1 !== $str2) {
            $temp1 = tempnam(TEMP_PATH, 'panel2tests');
            $temp2 = tempnam(TEMP_PATH, 'panel2tests');
            file_put_contents($temp1, $str1);
            file_put_contents($temp2, $str2);
            $cmd = 'diff --text -W 5000 ' . escapeshellarg($temp1) . ' ' . escapeshellarg($temp2);
            $diff = shell_exec($cmd);
            unlink($temp1);
            unlink($temp2);

            logg(
                "Different strings (%d bytes vs %d bytes):\n%s\n",
                strlen($str1),
                strlen($str2),
                $diff
            );
            $this->fail('strings are not equal (see DIFF above)');
        } else {
            $this->assertEquals($str1, $str2);
        }
    }

}