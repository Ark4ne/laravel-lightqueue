<?php

namespace Ark4ne\LightQueue;

interface LightQueueCommandInterface {

    /**
     * Execute the commands
     *
     * @param $data
     * @return mixed
     */
    public function fire($data);

}