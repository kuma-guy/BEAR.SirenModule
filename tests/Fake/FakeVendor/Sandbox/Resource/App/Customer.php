<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\SirenClass;

class Customer extends ResourceObject
{
    private $resource;
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @SirenClass(name="info,customer")
     *
     * @param $customerId
     *
     * @return $this
     */
    public function onGet($customerId)
    {
        $this['customerId'] = 'pj123';
        $this['name'] = 'Peter Joseph';

        return $this;
    }
}
