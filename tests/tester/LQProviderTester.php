<?php


abstract class LQProviderTester extends TestCase
{
    /**
     * @return \Ark4ne\LightQueue\Provider\ProviderInterface
     */
    protected abstract function getProvider();

    public function testCreate()
    {
        $fileQueue = $this->getProvider();
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

    public function testNull(){
        $this->assertNull($this->getProvider()->next());
    }
}