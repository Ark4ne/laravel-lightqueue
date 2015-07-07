<?php

use Ark4ne\LightQueue\LightQueue;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class LightQueueTest extends TestCase
{
    public function setUp()
    {
        $this->createApplication();

        Artisan::add(new \Ark4ne\LightQueue\Command\LightQueueCommand());
    }

    public function testPushQueue()
    {
        $queueTest = [];

        for($i = 0, $length = 5; $i < $length; $i++)
            $queueTest[]=(object)['data'=>'test.'.$i];

        foreach($queueTest as $queue){
            LightQueue::instance()->push('JobExemple', $queue, '');
        }

        $this->assertEquals(5, \Ark4ne\LightQueue\Manager\LightQueueManager::instance()->queueSize(''));

        foreach($queueTest as $key => $queue){
            $output = new BufferedOutput();

            Artisan::call('lq:exec', [], $output);
            $response = $output->fetch();
            $this->assertEquals(5-$key-1, \Ark4ne\LightQueue\Manager\LightQueueManager::instance()->queueSize(''));
            $this->assertEquals(json_encode($queue)."\n", $response);
        }

        $this->assertEquals(0, \Ark4ne\LightQueue\Manager\LightQueueManager::instance()->queueSize(''));
    }
}