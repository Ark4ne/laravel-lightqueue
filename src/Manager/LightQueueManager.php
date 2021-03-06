<?php

namespace Ark4ne\LightQueue\Manager;

use Ark4ne\LightQueue\Provider\CacheQueueProvider;
use Ark4ne\LightQueue\Provider\FileQueueProvider;
use Ark4ne\LightQueue\Provider\ProviderInterface;
use Ark4ne\Process\Command\Command;
use Ark4ne\Process\System\OS;
use Ark4ne\Process\System\System;
use Illuminate\Support\Facades\Config;

class LightQueueManager
{

	/**
	 * @var LightQueueManager
	 */
	private static $_instance = null;

	/**
	 * @param null $driver
	 *
	 * @return LightQueueManager
	 */
	public static function instance($driver = null)
	{
		if (self::$_instance == null) {
			self::$_instance = new self(!$driver ? Config::get('queue.lightqueue.driver') : $driver);
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

	private function __construct($driver)
	{
		$this->max_processes = 4;

		$this->setDriver($driver);
	}

	public function setDriver($driver)
	{
		switch ($driver) {
			case 'file':
				$this->driver = 'file';
				break;
			case 'cache':
			default:
				$this->driver = 'cache';
				break;
		}

		Config::set('queue.lightqueue.driver', $this->driver);
	}

	/**
	 * @param $queue
	 *
	 * @return ProviderInterface|null
	 */
	private function provider($queue)
	{
		$provider = null;
		switch ($this->driver) {
			case 'file':
				$provider = new FileQueueProvider($queue);
				break;
			case 'cache':
			default:
				$provider = new CacheQueueProvider($queue);
				break;
		}

		return $provider;
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
		if (is_null($queue)) {
			$queue = $this->queue;
		}
		else {
			$this->queue = $queue;
		}

		if (!array_key_exists($this->driver . $queue, $this->fileQueues)) {
			$this->fileQueues[$this->driver . $queue] = $this->provider($queue);
		}

		return $this->fileQueues[$this->driver . $queue];
	}

	/**
	 * Returns the number of active processes.
	 * @return int
	 */
	public function getActiveProcess()
	{
		//return intval(exec("ps aux | grep -v grep | grep 'php " . base_path() . "artisan lq:exec' | wc -l"));

		return System::countProcesses("php " . base_path() . "artisan lq:exec");
	}

	/**
	 * @return int
	 */
	public function getMaxProcesses()
	{
		return $this->max_processes;
	}

	/**
	 * @param int $max_processes
	 */
	public function setMaxProcesses($max_processes)
	{
		$this->max_processes = $max_processes;
	}

	/**
	 * Push a new job onto the queue.
	 *
	 * @param      $job
	 * @param      $data
	 * @param null $queue
	 */
	public function pushQueue($job, $data, $queue = null)
	{
		if (is_null($queue)) {
			$queue = $this->queue;
		}
		try {
			$this->queue($queue)->push(json_encode([
													   'job'  => $job,
													   'data' => $data,
												   ]));
			$this->createProcess($queue);
		}
		catch (\Exception $e) {
		}
	}

	/**
	 * Get the next onto the queue.
	 *
	 * @param null $queue
	 *
	 * @return mixed|null
	 */
	public function nextQueue($queue = null)
	{
		if (is_null($queue)) {
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

	public function queueSize($queue)
	{
		return $this->queue($queue)->queueSize();
	}

	/**
	 * Launch new job if the number of active processes is under maximum MAX_JOB_THREAD.
	 *
	 * @param null|string $queue
	 *
	 * @return bool
	 */
	public function createProcess($queue)
	{
		if ($this->getActiveProcess() < $this->max_processes) {
			//exec('php ' . base_path() . '/artisan lq:exec --queue="' . $queue . '"> /dev/null &');

			$cmd = new Command('php', base_path() . '/artisan lq:exec', 'queue="' . $queue . '"');
			$cmd->exec(true);

			return true;
		}

		return false;
	}

	/**
	 * Handle end of a job.
	 *
	 * @param null|string $queue
	 *
	 * @return void|bool
	 */
	public function jobDestruct($queue)
	{
		if ($this->queue($queue)->hasNext()) {
			return $this->createProcess($queue);
		}
	}
}
