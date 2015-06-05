<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceObject;

class OrderItems extends ResourceObject
{
    public function onGet()
    {
        return $this;
    }
}