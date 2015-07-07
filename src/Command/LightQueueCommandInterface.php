<?php

namespace Ark4ne\LightQueue\Command;

interface LightQueueCommandInterface {

    /**
     * Execute the commands
     *
     * @param $job
     * @param $data
     * @return mixed
     */
    public function fire($job, $data);

}