<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/07/2015
 * Time: 21:22
 */

use Ark4ne\LightQueue\Command\LightQueueCommand;
use Ark4ne\LightQueue\Manager\LightQueueManager;
use Ark4ne\LightQueue\Provider\FileQueueProvider;

class LightQueueCommandTest extends TestCase {

    public function setUp()
    {
        $this->createApplication();
        LightQueueManager::instance('file');
    }

    public function testCmd()
    {
        $fileQueue = new FileQueueProvider(null);

        $fileQueue->push('null');
        $fileQueue->push('null1');
        $fileQueue->push('null2');

        $cmd = new LightQueueCommand();

        $this->setExpectedException('Ark4ne\LightQueue\Exception\LightQueueException', 'LightQueueCommand data invalid');

        $this->assertEquals(0, $cmd->run(new IConsole(), new OConsole()));

        $this->assertEquals(2, $fileQueue->queueSize());

        $this->assertEquals('null1', $fileQueue->next());
        $this->assertEquals(1, $fileQueue->queueSize());

        $this->assertEquals('null2', $fileQueue->next());
        $this->assertFalse($fileQueue->hasNext());

    }
}
