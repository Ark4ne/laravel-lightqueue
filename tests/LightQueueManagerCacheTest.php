<?php

use Illuminate\Support\Facades\Config;

class LightManagerManagerCacheTest extends LQManagerTester
{
    protected function setUpConfig()
    {
        $this->driver = 'cache';
        $this->queueName = 'process';

        Config::set('queue.lightqueue.driver', 'cache');
    }
}
