<?php

use Illuminate\Support\Facades\Artisan;

class ArtisanTest extends TestCase
{

    public function setUp()
    {
        $this->createApplication();

        Artisan::add(new \Ark4ne\LightQueue\LightQueueCommand());
    }

    public function testCall()
    {
        Artisan::call('lq:exec');
    }
}