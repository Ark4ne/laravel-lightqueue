<?php

namespace Ark4ne\LightQueue\Provider;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheQueueProvider implements ProviderInterface
{
    private $queue;
    private $queue_name;
    private $md5_queue;

    /**
     * @param string $file_queue_name Name of queue
     */
    public function __construct($file_queue_name)
    {
        $this->md5_queue = md5($this->queue_name = $file_queue_name);
    }

    public function getQueue()
    {
        $this->queue == null && $this->queue = json_decode(Cache::get($this->md5_queue, '[]'));

        return $this->queue;
    }

    public function applyQueue()
    {
        Cache::forever($this->md5_queue, json_encode($this->queue));
    }

    public function addCmd($key)
    {
        if (!in_array($key, $this->getQueue())) {
            array_push($this->queue, $key);
            $this->applyQueue();
        }
    }

    /**
     * Push Command to queue.
     *
     * @param $cmd
     *
     * @return bool
     */
    public function push($cmd)
    {
        $key = md5($cmd);

        $this->addCmd($key);

        Cache::tags($this->queue_name)->put($key, $cmd, Carbon::now()->addHours(1));

        return true;
    }

    /**
     * @return int
     */
    public function queueSize()
    {
        return count($this->getQueue());
    }

    /**
     * Check if queue has command.
     *
     * @return bool
     */
    public function hasNext()
    {
        return count($this->getQueue()) > 0;
    }

    /**
     * Get the next command in queue.
     *
     * @return string
     */
    public function next()
    {
        $this->getQueue();
        if (count($this->queue)) {
            $key = array_shift($this->queue);
            $cmd = Cache::tags($this->queue_name)->get($key);
            Cache::tags($this->queue_name)->forget($key);

            $this->applyQueue();

            return $cmd;
        }

        return null;
    }
}