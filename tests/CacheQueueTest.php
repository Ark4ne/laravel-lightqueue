<?php

use Ark4ne\LightQueue\Provider\CacheQueueProvider;

class CacheQueueTest extends TestCase
{

    public function testCreate()
    {
        $fileQueue = new CacheQueueProvider('test');

        $string_test = "string_test";

        $this->assertEquals(0, $fileQueue->queueSize());

        $this->assertTrue($fileQueue->push($string_test));

        $this->assertTrue(is_array($fileQueue->getQueue()));

        $this->assertEquals(1, $fileQueue->queueSize());

        $this->assertTrue($fileQueue->hasNext());

        $this->assertEquals($string_test, $fileQueue->next());

        $this->assertEquals(0, $fileQueue->queueSize());

        $this->assertFalse($fileQueue->hasNext());
    }
}

