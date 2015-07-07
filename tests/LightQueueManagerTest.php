<?php

use Illuminate\Support\Facades\Config;

class LightManagerManagerTest extends LQManagerTester
{
    protected function setUpConfig()
    {
        $this->driver = Config::get('queue.lightqueue.driver');
        $this->queueName = 'process';
    }
}