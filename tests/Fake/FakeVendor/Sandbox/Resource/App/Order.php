<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\Annotation\Link;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
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
     * @Link(rel="customer", href="app://self/customer{?customerId}", method="get")
     *
     * @param $orderNumber
     * @return $this
     */
    public function onGet($orderNumber)
    {
        $this['orderNumber'] = $orderNumber;
        $this['itemCount']   = 3;
        $this['status']      = "pending";

        $customer = [
            'customerId' => "pj123"
        ];

        // TODO: How to implement Sub Entities BODY
        $this->resource->href('customer', $customer);


        return $this;
    }

    /**
     * @Name("add-item")
     * @Title("Add Item")
     */
    public function onPost()
    {
    }

    /**
     * @Name("delete-item")
     * @Title("Delete Item")
     */
    public function onDelete()
    {
    }

    /**
     * @Name("update-item")
     * @Title("Update Item")
     */
    public function onPut()
    {
    }
}