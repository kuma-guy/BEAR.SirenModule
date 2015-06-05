<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenRenderer\Annotation\Name;
use BEAR\SirenRenderer\Annotation\Rel;
use BEAR\SirenRenderer\Annotation\SubEntity;
use BEAR\SirenRenderer\Annotation\Title;

class Customer extends ResourceObject
{
    private $resource;
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @Rel("http://x.io/rels/customer")
     */
    public function onGet($customerId)
    {
        $this['customerId'] = "pj123";
        $this['name'] = "Peter Joseph";
        return $this;
    }
}