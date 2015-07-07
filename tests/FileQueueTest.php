<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 06/07/2015
 * Time: 22:28
 */

use Ark4ne\LightQueue\Provider\FileQueueProvider;
use Ark4ne\LightQueue\Exception\LightQueueException;


class FileQueueTest extends TestCase
{

    public function builderTest()
    {
        $fileQueue = new FileQueueProvider('test');
        $string_test = "string_test";
        for ($i = 0, $length = 10; $i < $length; $i++) {
            $this->assertTrue($fileQueue->push($string_test . $i));
            $this->assertEquals($i + 1, $fileQueue->queueSize());
        }
        for ($i = 0, $length = 10; $i < $length; $i++) {
            $this->assertEquals($string_test . $i, $fileQueue->next());
            $this->assertEquals($length - 1 - $i, $fileQueue->queueSize());
        }
        $this->assertFalse($fileQueue->hasNext());
        $this->assertEquals(0, $fileQueue->queueSize());
    }

    public function testCreate()
    {
        $this->builderTest();
    }

    public function testFileHandleException()
    {
        $this->setExpectedException('Ark4ne\LightQueue\Exception\LightQueueException', "FileQueueProvider::__construct: Can't create queue file");
        $fileQueue = new FileQueueProvider('te/feg/hhth/ht/htrt');

        $this->setExpectedException(null);
        $this->assertFalse($fileQueue->push(''));
    }
}

