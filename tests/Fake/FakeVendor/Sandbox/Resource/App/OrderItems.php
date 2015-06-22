<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\SirenClass;
use BEAR\SirenModule\Annotation\SirenField;
use BEAR\SirenModule\Annotation\SirenName;
use BEAR\SirenModule\Annotation\SirenTitle;

class OrderItems extends ResourceObject
{
    /**
     * @SirenClass(name="items,collection")
     *
     * @return $this
     */
    public function onGet($orderNumber)
    {
        return $this;
    }

    /**
     * @SirenName("add-item")
     * @SirenTitle("Add Item")
     * @SirenField(name="orderNumber", type="hidden", value="{?orderNumber}")
     * @SirenField(name="productCode", type="text")
     * @SirenField(name="quantity", type="number")
     *
     * @param int $customerId
     * @return $this
     */
    public function onPost($customerId)
    {
        // do something...
        return $this;
    }
}
