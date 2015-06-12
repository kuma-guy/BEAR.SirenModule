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
        // class: [ "info", "customer" ]
        // rel: [ "http://x.io/rels/customer" ]
        // links :  { "rel": [ "self" ], "href": "http://api.x.io/customers/pj123" }

        // Going to be properties
        $this['customerId'] = 'pj123';
        $this['name'] = 'Peter Joseph';

        return $this;
    }
}
