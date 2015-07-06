<?php

namespace Ark4ne\LightQueue;

class LightQueueManager {

    private static $_instance = null;

    public static function instance()
    {
        if (self::$_instance == null){
            self::$_instance = new LightQueueManager();
        }
        return self::$_instance;
    }

    /**
     * @var FileQueue
     */
    private $fileQueue;

    private function __construct(){
        $this->fileQueue = new FileQueue();
    }

    public function getActiveProcess()
    {
        return intval(exec("ps -ufxa | grep -v grep | grep 'php ".base_path()."artisan lq:exec' | wc -l"));
    }

    public function pushQueue($job, $data)
    {
        try {
            $this->fileQueue->push(json_encode([
                'job' => $job,
                'data' => $data,
            ]));
            $this->createProcess();
        } catch (\Exception $e){

        }
    }

    public function nextQueue()
    {
        $sQueueCmd = $this->fileQueue->next();
        if(strlen($sQueueCmd) > 0){
          $queueCmd = json_decode($sQueueCmd);
            if(isset($queueCmd->job)){
                return $queueCmd;
            }
        }
        return null;
    }

    public function createProcess()
    {
        if ($this->getActiveProcess() < 8) {
            exec("php " . base_path() . "/artisan lq:exec > /dev/null &");
        }
    }

    public function deleteProcess()
    {
        if ($this->fileQueue->hasNext())
            $this->createProcess();
    }
}