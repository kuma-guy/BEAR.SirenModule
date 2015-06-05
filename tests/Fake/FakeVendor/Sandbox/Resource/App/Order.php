<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenRenderer\Annotation\Field;
use BEAR\SirenRenderer\Annotation\Name;
use BEAR\SirenRenderer\Annotation\SubEntity;
use BEAR\SirenRenderer\Annotation\Title;

class Order extends ResourceObject
{
    private $resource;
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @Name("get-item")
     * @Title("Get Item")
     *
     * @Embed(rel="customer", src="app://self/customer{?customerId}")
     * @Embed(rel="item", src="app://self/item")
     *
     * @param $orderNumber
     * @return $this
     */
    public function onGet($orderNumber)
    {
        $this['orderNumber'] = $orderNumber;
        $this['itemCount']   = 3;
        $this['status']      = "pending";

        $customerId = "pj123";

        $this['customer']->addQuery(['customerId' => $customerId])->eager->request();
        $this['item'] = [];

        return $this;
    }
}