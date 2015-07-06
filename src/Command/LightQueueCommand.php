<?php
namespace Ark4ne\LightQueue;

use \Illuminate\Console\Command;

class LightQueueCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lq:exec';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'LightQueue: Execution next command in queue';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws LightQueueException
     * @return mixed
     */
    public function fire()
    {
        $queueCmd = LightQueueManager::instance()->nextQueue();
        if (!is_null($queueCmd) && isset($queueCmd->job)) {
            $job = new $queueCmd->job();

            if ($job instanceof LightQueueCommandInterface)
                $job->fire($queueCmd->data);
            else
                throw new LightQueueException("$queueCmd->job not implement LightQueueCommandInterface");
        }
    }

    public function __destruct()
    {
        LightQueueManager::instance()->deleteProcess();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}