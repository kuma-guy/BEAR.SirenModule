<?php

namespace BEAR\SirenRenderer;

use BEAR\Resource\ResourceInterface;
use FakeVendor\Sandbox\AppModule;
use Ray\Di\Injector;

class SirenRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $resource = (new Injector(new AppModule()))->getInstance(ResourceInterface::class);
        // request
        $order = $resource
            ->get
            ->uri('app://self/order')
            ->withQuery(['orderNumber' => 42])
            ->eager
            ->request();

        var_dump((string) $order);
        die();


        $this->assertSame($expect, (string) $news);
    }
}