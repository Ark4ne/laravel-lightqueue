<?php
namespace Ark4ne\LightQueue\Command;

use Ark4ne\LightQueue\Exception\LightQueueException;
use Ark4ne\LightQueue\Manager\LightQueueManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

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
     * Name of current queue.
     *
     * @var string
     */
    private $queue;

    /**
     * Job Object
     *
     * @var object
     */
    private $cmd;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check if data
     *
     * @param $cmd
     * @return bool
     */
    private function isValidCmd($cmd)
    {
        try {
            if (!is_null($cmd) && is_string($cmd->job)) {
                return true;
            }
        } catch (\Exception $e) {

        }
        return false;
    }

    /**
     * Execute the console command.
     *
     * @throws LightQueueException
     * @return mixed
     */
    public function fire()
    {
        $this->queue = @$this->option('queue');
        if (!$this->queue) $this->queue = 'process';
        $this->cmd = LightQueueManager::instance()->nextQueue($this->queue);

        if ($this->isValidCmd($this->cmd)) {
            $job = new $this->cmd->job();

            if ($job instanceof LightQueueCommandInterface)
                $job->fire($this, $this->cmd->data);
            else
                throw new LightQueueException("{$this->cmd->job} not implement LightQueueCommandInterface");
        } else
            throw new LightQueueException("LightQueueCommand data invalid");
    }

    public function __destruct()
    {
        LightQueueManager::instance()->jobDestruct($this->queue);
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
        return [['queue', null, InputOption::VALUE_OPTIONAL, 'Queue name', null]];
    }
}