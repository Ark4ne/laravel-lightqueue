<?php

use Ark4ne\LightQueue\LightQueue;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class LightQueueTest extends TestCase
{
    public function setUp()
    {
        $this->createApplication();

        Artisan::add(new \Ark4ne\LightQueue\LightQueueCommand());
    }

    public function testPushQueue()
    {
        LightQueue::instance()->push('JobExemple');

        $output = new BufferedOutput();

        Artisan::call('lq:exec', array(), $output);
        $response = $output->fetch();
        $this->assertEquals('', $response);
    }
}