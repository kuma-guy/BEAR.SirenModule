<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenRenderer\Annotation\Action;

class Order extends ResourceObject
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @Embed(rel="customer", src="app://self/customer{?customerId}")
     * @Embed(rel="order-items", src="app://self/orderitem{?orderNumber}")
     * @Action(src="app://self/orderitem{?orderNumber}", method="post")
     *
     * @param $orderNumber
     * @return $this
     */
    public function onGet($orderNumber)
    {
        $this['orderNumber'] = $orderNumber;
        $this['itemCount'] = 3;
        $this['status'] = "pending";

        $customerId = "pj123";

        $this['customer']->addQuery(['customerId' => $customerId])->eager->request();
        $this['order-items'] = [];

        return $this;
    }
}