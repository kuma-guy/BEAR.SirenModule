<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\SirenAction;
use BEAR\SirenModule\Annotation\SirenClass;
use BEAR\SirenModule\Annotation\SirenEmbedLink;
use BEAR\SirenModule\Annotation\SirenEmbedResource;

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
     * @SirenEmbedLink(rel="order-items", src="app://self/orderitem{?orderNumber}")
     * @SirenAction(src="app://self/orderitem{?orderNumber}", method="post")
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
