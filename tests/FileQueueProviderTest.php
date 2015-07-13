<?php

use Ark4ne\LightQueue\Provider\FileQueueProvider;
use Illuminate\Support\Facades\Config;

class FileQueueProviderTest extends LQProviderTester
{

	private $provider;

	/**
	 * @return \Ark4ne\LightQueue\Provider\ProviderInterface
	 */
	protected function getProvider()
	{
		if ($this->provider == null) {
			$this->provider = new FileQueueProvider('test');
		}

		return $this->provider;
	}

	public function testFileHandleException()
	{
		$this->setExpectedException('Ark4ne\LightQueue\Exception\LightQueueException',
									"FileLockable::__construct: Can't create queue file");
		$directory = Config::get('queue.lightqueue.queue_directory');
		Config::set('queue.lightqueue.queue_directory', 'Wrong/Directory/Path/');
		$fileQueue = new FileQueueProvider('te/feg/hhth/ht/htrt');
		$this->setExpectedException(null);
		$this->assertFalse($fileQueue->push(''));
		Config::set('queue.lightqueue.queue_directory', $directory);
	}

	public function testFileLockException()
	{
		$file  = new \Ark4ne\LightQueue\Provider\FileLockable(Config::get('queue.lightqueue.queue_directory') . md5('testFileLockException') . ".queue");
		$queue = new FileQueueProvider('testFileLockException');

		$file->handle();

		$this->assertEquals($queue->getFile()->getPath(), $file->getPath());

		$this->assertTrue($file->lock());
		$this->assertFalse($queue->push('test'));
		$this->assertTrue($file->unlock());

		$this->assertTrue($queue->push('test'));

		$this->assertTrue($file->lock());
		$this->assertNull($queue->next());
		$this->assertTrue($file->unlock());

		$this->assertNotNull($queue->next());
	}
}
