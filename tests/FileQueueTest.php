<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 06/07/2015
 * Time: 22:28
 */

use Ark4ne\LightQueue\Provider\FileQueueProvider;

class FileQueueTest extends TestCase
{

    public function testCreate()
    {
        $fileQueue = new FileQueueProvider('test');

        $string_test = "string_test";

        $this->assertEquals(0, $fileQueue->queueSize());

        $this->assertTrue($fileQueue->push($string_test));

        $this->assertEquals(1, $fileQueue->queueSize());

        $this->assertTrue($fileQueue->hasNext());

        $this->assertEquals($string_test, $fileQueue->next());

        $this->assertEquals(0, $fileQueue->queueSize());

        $this->assertFalse($fileQueue->hasNext());
    }
}

