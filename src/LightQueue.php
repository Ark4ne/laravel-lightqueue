<?php

namespace Ark4ne\LightQueue;

use Ark4ne\LightQueue\Manager\LightQueueManager;
use Illuminate\Queue\QueueInterface;

class LightQueue implements QueueInterface
{

    /**
     * @var LightQueueManager
     */
    private $manager;

    private function __construct()
    {
        $this->manager = LightQueueManager::instance();
    }


    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  mixed $data
     * @param  string $queue
     * @return mixed
     */
    public function push($job, $data = null, $queue = null)
    {
        $this->manager->pushQueue($job, $data, $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string $payload
     * @param  string $queue
     * @param  array $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        $this->push($payload, $options, $queue);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTime|int $delay
     * @param  string $job
     * @param  mixed $data
     * @param  string $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $this->push($job, $data, $queue);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string $queue
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null)
    {
        return $this->manager->nextQueue($queue);
    }

    private static $_i;

    public static function instance()
    {
        if (self::$_i == null)
            self::$_i = new LightQueue();

        return self::$_i;
    }
}