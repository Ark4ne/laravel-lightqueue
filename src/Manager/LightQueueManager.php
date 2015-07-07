<?php

namespace Ark4ne\LightQueue\Manager;

use Ark4ne\LightQueue\Provider\CacheQueueProvider;
use Ark4ne\LightQueue\Provider\FileQueueProvider;
use Ark4ne\LightQueue\Provider\ProviderInterface;
use Illuminate\Support\Facades\Config;

class LightQueueManager
{

    /**
     * @var LightQueueManager
     */
    private static $_instance = null;

    /**
     * @return LightQueueManager
     */
    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new LightQueueManager();
        }
        return self::$_instance;
    }

    /**
     * @var string
     */
    private $queue = 'process';

    /**
     * @var int
     */
    private $max_processes;

    /**
     * @var string
     */
    private $driver;
    /**
     * @var array
     */
    private $fileQueues = [];

    private function __construct()
    {
        $this->max_processes = Config::get('queue.lightqueue.processes.max_by_queue');
        switch (Config::get('queue.lightqueue.driver')) {
            case 'file':
                $this->driver = 'file';
                break;
            case 'cache':
            default:
                $this->driver = 'cache';
                break;
        }
    }

    private function provider($queue)
    {
        switch ($this->driver) {
            case 'file':
                return new FileQueueProvider($queue);
                break;
            case 'cache':
            default:
                return new CacheQueueProvider($queue);
                break;
        }
    }

    /**
     * Get FileQueueProvider by queue name.
     *
     * @param null|string $queue
     *
     * @return ProviderInterface
     */
    private function queue($queue = null)
    {
        if (!$queue)
            $queue = $this->queue;
        else
            $this->queue = $queue;

        if (!array_key_exists($queue, $this->fileQueues))
            $this->fileQueues[$queue] = $this->provider($queue);


        return $this->fileQueues[$queue];
    }

    /**
     * Returns the number of active processes.
     *
     * @return int
     */
    public function getActiveProcess()
    {
        return intval(exec("ps aux | grep -v grep | grep 'php " . base_path() . "artisan lq:exec' | wc -l"));
    }

    /**
     * Push a new job onto the queue.
     *
     * @param $job
     * @param $data
     * @param null $queue
     */
    public function pushQueue($job, $data, $queue = null)
    {
        if (!$queue) {
            $queue = $this->queue;
        }
        try {
            $this->queue($queue)->push(json_encode([
                'job' => $job,
                'data' => $data,
            ]));
            $this->createProcess($queue);
        } catch (\Exception $e) {

        }
    }

    /**
     * Get the next onto the queue.
     *
     * @param null $queue
     * @return mixed|null
     */
    public function nextQueue($queue = null)
    {
        if (!$queue) {
            $queue = $this->queue;
        }
        $sQueueCmd = $this->queue($queue)->next();
        if (strlen($sQueueCmd) > 0) {
            $queueCmd = json_decode($sQueueCmd);
            if ($queueCmd && isset($queueCmd->job)) {
                $queueCmd->queue = $queue;
                return $queueCmd;
            }
        }
        return null;
    }

    public function queueSize($queue){
        return $this->queue($queue)->queueSize();
    }
    /**
     * Launch new job if the number of active processes is under maximum MAX_JOB_THREAD.
     *
     * @param null|string $queue
     */
    public function createProcess($queue)
    {
        if ($this->getActiveProcess() < $this->max_processes) {
            exec('php ' . base_path() . '/artisan lq:exec --queue="' . $queue . '"> /dev/null &');
        }
    }

    /**
     * Handle end of a job.
     *
     * @param null|string $queue
     */
    public function jobDestruct($queue)
    {
        if ($this->queue($queue)->hasNext())
            $this->createProcess($queue);
    }
}