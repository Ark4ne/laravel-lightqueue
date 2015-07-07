<?php

class TestCase extends Orchestra\Testbench\TestCase {

    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return array(
        );
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        //$app['path.base'] = __DIR__ . '/../src';

        // load custom config
        $config = require 'config/queue.php';

        // overwrite database configuration
        $app['config']->set('queue.lightqueue.queue_directory', $config['ligthqueue']['queue_directory']);

        // overwrite cache configuration
        $app['config']->set('cache.driver', 'array');
    }

}
