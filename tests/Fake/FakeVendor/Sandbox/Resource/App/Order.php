<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;

class Order extends ResourceObject
{
    private $resource;
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function onGet($orderNumber)
    {
        $this['itemCount'] = 3;
        $this['status']    = "pending";
        return $this;
    }
}