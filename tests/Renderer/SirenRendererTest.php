<?php

namespace BEAR\SirenModule;

use BEAR\Resource\ResourceInterface;
use FakeVendor\Sandbox\AppModule;
use Ray\Di\Injector;

class SirenRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $resource = (new Injector(new AppModule()))->getInstance(ResourceInterface::class);
        // request
        $order = $resource
            ->get
            ->uri('app://self/orders')
            ->withQuery(['orderNumber' => 42])
            ->eager
            ->request();

        $response = json_decode((string)$order);

        // class
        $this->assertSame('order', $response->class[0]);

        return $response;
    }

    /**
     * @depends testClass
     */
    public function testProperties($response)
    {
        // orderNumber
        $this->assertSame(42, $response->properties->orderNumber);
        // itemCount
        $this->assertSame(3, $response->properties->itemCount);
        // status
        $this->assertSame('pending', $response->properties->status);
    }

    /**
     * @depends testClass
     */
    public function testEntities($response)
    {
        foreach ($response->entities as $entity) {
            if (isset($entity->properties)) {
                // Class
                $this->assertSame('info', $entity->class[0]);
                $this->assertSame('customer', $entity->class[1]);
                // Rel
                $this->assertSame('customer', $entity->rel[0]);
                // Properties
                $this->assertSame('pj123', $entity->properties->customerId);
                $this->assertSame('Peter Joseph', $entity->properties->name);
                // Href
                $this->assertSame('/customer?customerId=pj123', $entity->href);

            } else {
                // Class
                $this->assertSame('items', $entity->class[0]);
                $this->assertSame('collection', $entity->class[1]);
                // Rel
                $this->assertSame('order-items', $entity->rel[0]);
                // Href
                $this->assertSame('/order/items?orderNumber=42', $entity->href);
            }
        }
    }

    /**
     * @depends testClass
     */
    public function testActions($response)
    {
        // Name
        $this->assertSame('add-item', $response->actions[0]->name);
        // Title
        $this->assertSame('Add Item', $response->actions[0]->title);
        // Method
        $this->assertSame('POST', $response->actions[0]->method);
        // Href
        $this->assertSame('/order/items?orderNumber=42', $response->actions[0]->href);
        // Type
        $this->assertSame('application/x-www-form-urlencoded', $response->actions[0]->type);
        // Fields
        $this->assertSame('orderNumber', $response->actions[0]->fields[0]->name);
        $this->assertSame('hidden', $response->actions[0]->fields[0]->type);
        $this->assertSame('42', $response->actions[0]->fields[0]->value);
        $this->assertSame('productCode', $response->actions[0]->fields[1]->name);
        $this->assertSame('text', $response->actions[0]->fields[1]->type);
        $this->assertSame('quantity', $response->actions[0]->fields[2]->name);
        $this->assertSame('number', $response->actions[0]->fields[2]->type);
    }

    /**
     * @depends testClass
     */
    public function testLinks($response)
    {
        // Self
        $this->assertSame('self', $response->links[0]->rel[0]);
        $this->assertSame('/orders?orderNumber=42', $response->links[0]->href);
        // Previous
        $this->assertSame('previous', $response->links[1]->rel[0]);
        $this->assertSame('/orders?orderNumber=41', $response->links[1]->href);
        // Next
        $this->assertSame('next', $response->links[2]->rel[0]);
        $this->assertSame('/orders?orderNumber=43', $response->links[2]->href);
    }
}
