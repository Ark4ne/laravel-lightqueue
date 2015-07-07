<?php

use Ark4ne\LightQueue\Provider\CacheQueueProvider;

class CacheQueueTest extends TestCase
{


    public function builderTest()
    {
        $fileQueue = new CacheQueueProvider('test');
        $string_test = "string_test";
        for ($i = 0, $length = 10; $i < $length; $i++) {
            $this->assertTrue($fileQueue->push($string_test . $i));
            $this->assertEquals($i+1, $fileQueue->queueSize());
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
}

