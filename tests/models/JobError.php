<?php

class JobError {

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