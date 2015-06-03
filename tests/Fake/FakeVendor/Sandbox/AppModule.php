<?php

namespace FakeVendor\Sandbox;

use BEAR\Resource\Module\ResourceModule;
use BEAR\SirenRenderer\Context\SirenModule;
use Ray\Di\AbstractModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new ResourceModule('FakeVendor\Sandbox'));
        $this->override(new SirenModule);
    }
}