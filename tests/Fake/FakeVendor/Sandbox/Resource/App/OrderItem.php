<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\Field;
use BEAR\SirenModule\Annotation\Name;
use BEAR\SirenModule\Annotation\SirenClass;
use BEAR\SirenModule\Annotation\Title;

class OrderItem extends ResourceObject
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
     * @Name("add-item")
     * @Title("Add Item")
     *
     * @Field(name="orderNumber", type="hidden", value="{?orderNumber}")
     * @Field(name="productCode", type="text")
     * @Field(name="quantity", type="number")
     *
     * @param int $customerId
     */
    public function onPost($customerId)
    {
        // do something...
    }
}
