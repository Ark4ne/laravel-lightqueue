<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/07/2015
 * Time: 05:09
 */

class JobExemple implements Ark4ne\LightQueue\LightQueueCommandInterface {

    /**
     * Execute the commands
     *
     * @param $data
     * @return mixed
     */
    public function fire($data)
    {
        throw new \Exception('JobExemple::fire');
    }
}