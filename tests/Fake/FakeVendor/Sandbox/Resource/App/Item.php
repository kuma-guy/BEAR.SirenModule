<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceObject;

class Item extends ResourceObject
{
    public function onGet()
    {
        $this['item'] = 'item';
        return $this;
    }
}