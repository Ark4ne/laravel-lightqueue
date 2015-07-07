<?php

class JobValid implements Ark4ne\LightQueue\Command\LightQueueCommandInterface {

    /**
     * Execute the commands
     *
     * @param $job object
     * @param $data mixed
     * @return mixed
     * @throws Exception
     */
    public function fire($job, $data)
    {
        $job->info(json_encode($data));
    }
}