<?php

namespace Ark4ne\LightQueue;

class LightQueueManager
{

    private static $_instance = null;

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
     * @var array
     */
    private $fileQueues = [];

    private function __construct()
    {
    }

    /**
     * Get FileQueue by queue name.
     *
     * @param null|string $queue
     *
     * @return FileQueue
     */
    private function queue($queue = null)
    {
        if (!$queue)
            $queue = $this->queue;
        else
            $this->queue = $queue;

        if (!array_key_exists($queue, $this->fileQueues))
            $this->fileQueues[$queue] = new  FileQueue($queue);


        return $this->fileQueues[$queue];
    }

    /**
     * Returns the number of active processes.
     *
     * @return int
     */
    public function getActiveProcess()
    {
        return intval(exec("ps -ufxa | grep -v grep | grep 'php " . base_path() . "artisan lq:exec' | wc -l"));
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
            $this->createProcess();
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

    /**
     * Launch new job if the number of active processes is under maximum MAX_JOB_THREAD.
     */
    public function createProcess()
    {
        if ($this->getActiveProcess() < 8) {
            exec("php " . base_path() . "/artisan lq:exec > /dev/null &");
        }
    }

    /**
     * Handle end of a job.
     */
    public function jobDestruct($queue = null)
    {
        if ($this->queue($queue)->hasNext())
            $this->createProcess();
    }
}