<?php

use Ark4ne\LightQueue\Provider\CacheQueueProvider;

class CacheQueueProviderTest extends LQProviderTester
{
    private $provider;

    /**
     * @return \Ark4ne\LightQueue\Provider\ProviderInterface
     */
    protected function getProvider()
    {
        if ($this->provider == null)
            $this->provider = new CacheQueueProvider('test');

        return $this->provider;
    }
}
