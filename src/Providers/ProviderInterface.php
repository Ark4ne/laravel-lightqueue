<?php

namespace Ark4ne\LightQueue\Provider;

interface ProviderInterface
{
    /**
     * @param string $file_queue_name Name of queue
     */
    public function __construct($file_queue_name);

    /**
     * Get size of queue
     *
     * @return int
     */
    public function queueSize();

    /**
     * Push Command to queue
     *
     * @param $cmd
     * @return bool
     */
    public function push($cmd);

    /**
     * Check if queue has command
     *
     * @return bool
     */
    public function hasNext();

    /**
     * Get the next command in queue
     *
     * @return string
     */
    public function next();
}