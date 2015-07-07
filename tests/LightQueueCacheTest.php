<?php

use Ark4ne\LightQueue\LightQueue;
use Ark4ne\LightQueue\Manager\LightQueueManager;
use Ark4ne\LightQueue\Exception\LightQueueException;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class LightQueueCacheTest extends TestCase
{

    private $queueName = "process";

    public function setUp()
    {
        $this->createApplication();

        Artisan::add(new \Ark4ne\LightQueue\Command\LightQueueCommand());

        LightQueueManager::instance()->setDriver('cache');
    }

    public function builderTestPush($_func_lightQueue, $jobName)
    {
        $queueTest = [];

        for ($i = 0, $length = 5; $i < $length; $i++)
            $queueTest[] = (object)['data' => 'test.' . $i];

        foreach ($queueTest as $key => $data) {
            $_func_lightQueue($jobName, $data, $this->queueName);
        }

        return $queueTest;
    }

    public function builderExecTest($queueTest)
    {
        $this->assertEquals(5, LightQueueManager::instance()->queueSize($this->queueName));

        foreach ($queueTest as $key => $data) {
            $output = new BufferedOutput();

            Artisan::call('lq:exec', [], $output);
            $response = $output->fetch();
            $this->assertEquals(5 - $key - 1, LightQueueManager::instance()->queueSize($this->queueName));
            $this->assertEquals(json_encode($data) . "\n", $response);
        }
        $this->assertEquals(0, LightQueueManager::instance()->queueSize($this->queueName));
    }

    public function testPopQueue()
    {
        $this->builderTestPush(function ($job, $data, $queue) {
            LightQueue::instance()->push($job, $data, $queue);
            $this->assertEquals((object)[
                'job' => 'JobValid',
                'data' => $data,
                'queue' => $this->queueName,
            ], LightQueue::instance()->pop($queue));
        }, 'JobValid');
    }

    public function testPushQueue()
    {
        $this->builderExecTest($this->builderTestPush(function ($job, $data, $queue) {
            LightQueue::instance()->push($job, $data, $queue);
        }, 'JobValid'));
    }

    public function testPushRawQueue()
    {
        $this->builderExecTest($this->builderTestPush(function ($job, $data, $queue) {
            LightQueue::instance()->pushRaw($job, $queue, (array)$data);
        }, 'JobValid'));
    }

    public function testLaterQueue()
    {
        $this->builderExecTest($this->builderTestPush(function ($job, $data, $queue) {
            LightQueue::instance()->later(null, $job, $data, $queue);
        }, 'JobValid'));
    }

    public function testExceptionJob()
    {
        $job = 'JobError';
        LightQueue::instance()->push($job, ['data' => 'test'], '');
        $this->assertEquals(1, LightQueueManager::instance()->queueSize(''));
        $output = new BufferedOutput();
        $this->setExpectedException('Ark4ne\LightQueue\Exception\LightQueueException');
        Artisan::call('lq:exec', [], $output);
        $LightQueueException = false;
        try {
            $response = $output->fetch();
        } catch (LightQueueException $e) {
            $LightQueueException = true;
            $this->assertEquals('JobError not implement LightQueueCommandInterface', $e->getMessage());
            $response = null;
        }
        $this->assertEquals(true, $LightQueueException);
        $this->assertEquals(null, $response);
    }
}