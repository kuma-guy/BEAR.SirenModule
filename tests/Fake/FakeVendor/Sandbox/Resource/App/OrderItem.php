<?php

namespace FakeVendor\Sandbox\Resource\App;

use BEAR\Resource\ResourceObject;
use BEAR\SirenModule\Annotation\Field;
use BEAR\SirenModule\Annotation\Name;
use BEAR\SirenModule\Annotation\Title;

class OrderItem extends ResourceObject
{
    /**
     * Class(name="items,collection")?
     * Rel?
     *
     * @return $this
     */
    public function onGet($orderNumber)
    {
        // class: [ "items", "collection" ]
        // rel: [ "http://x.io/rels/order-items" ]
        // href: http://api.x.io/orders/42/items
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
    }
}
