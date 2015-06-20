<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\SirenAction;
use BEAR\SirenModule\Annotation\SirenClass;
use BEAR\SirenModule\Annotation\SirenEmbedLink;
use BEAR\SirenModule\Annotation\SirenEmbedResource;
use BEAR\SirenModule\Annotation\SirenLink;

class Orders extends ResourceObject
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @SirenClass(name="order")
     * @SirenEmbedResource(rel="customer", src="app://self/customer{?customerId}")
     * @SirenEmbedLink(rel="order-items", src="app://self/orderitems{?orderNumber}")
     * @SirenAction(src="app://self/orderitems{?orderNumber}", method="post")
     * @SirenLink(rel="previous", param="orderNumber")
     * @SirenLink(rel="next", param="orderNumber")
     *
     * @param $orderNumber
     *
     * @return $this
     */
    public function onGet($orderNumber)
    {
        $this['orderNumber'] = $orderNumber;
        $this['itemCount'] = 3;
        $this['status'] = 'pending';

        $customerId = 'pj123';
        $this['customer']->addQuery(['customerId' => $customerId])->eager->request();

        return $this;
    }
}
