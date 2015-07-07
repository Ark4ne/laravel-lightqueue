<?php

use Illuminate\Support\Facades\Config;

class LightManagerManagerFileTest extends LQManagerTester
{

    protected function setUpConfig()
    {
        $this->driver = 'file';
        $this->queueName = 'process';

        Config::set('queue.lightqueue.driver', 'file');
    }
}
