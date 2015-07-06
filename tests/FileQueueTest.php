<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 06/07/2015
 * Time: 22:28
 */

use Ark4ne\LightQueue\FileQueue;

class FileQueueTest extends PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $fileQueue = new FileQueue('test');

        $string_test = "string_test";

        $this->assertTrue($fileQueue->push($string_test), '$fileQueue->push');

        $this->assertTrue($fileQueue->hasNext(), '$fileQueue->hasNext');

        $this->assertEquals($string_test, $fileQueue->next(), '$fileQueue->next');

        $this->assertFalse($fileQueue->hasNext(), '$fileQueue->hasNext');
    }
}