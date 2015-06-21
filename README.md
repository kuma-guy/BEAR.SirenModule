# BEAR.SirenModule

Siren support for BEAR.Sunday

**Siren renderer**

It overrides default renderer and represents your resource in Siren format.

Siren [https://github.com/kevinswiber/siren]

## Entity

#### class (optional)

You can specify this optional value with `@SirenClass` annotation.

```
@SirenClass(name="order")
```

#### properties (optional)

Properties are the response body of the resource object.

## Entities

Add sub related resource entities using `@SirenEmbedResource` annotation.

```
@SirenEmbedResource(rel="customer", src="app://self/customer{?customerId}")
```

And then, you can embed the entity by request like below.

```
$this['customer']->addQuery(['customerId' => $customerId])->eager->request();
```

For sub related link entity use `@SirenEmbedLink` annotation.


```
@EmbedLink(rel="order-items", src="app://self/orderitem{?orderNumber}")
```

#### type (optional)

WIP

## Actions

Action can be added using `@SirenAction` annotation.

```
@SirenAction(src="app://self/orderitem{?orderNumber}", method="post")
```

The actual method defined as `SirenAction` has to be annotated like below.

```php
    /**
     * @SirenName("add-item")
     * @SirenTitle("Add Item")
     *
     * @SirenField(name="orderNumber", type="hidden", value="{?orderNumber}")
     * @SirenField(name="productCode", type="text")
     * @SirenField(name="quantity", type="number")
     *
     * @param int $customerId
     */
    public function onPost($customerId)
    {
        // do something...
    }
```

#### name (required)

You need to define action name using `@SirenName` annotation when you want to represent `Action`

#### title (optional)

This is optional. You can specify with `@SirenTitle` annotation.

#### field (optional)

This is going to be controls of the action.
You can add user control for the action with `@SirenField` annotation.


## Links

```
@SirenLink(rel="previous", param="orderNumber")
@SirenLink(rel="next", param="orderNumber")
```

## Example

#### Order Resource

```php
    /**
     * @SirenClass(name="order")
     *
     * @SirenEmbedResource(rel="customer", src="app://self/customer{?customerId}")
     * @SirenEmbedLink(rel="order-items", src="app://self/orderitems{?orderNumber}")
     *
     * @SirenAction(src="app://self/orderitems{?orderNumber}", method="post")
     *
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
```

#### Customer Resource

```php
    /**
     * @SirenClass(name="info,customer")
     *
     * @param $customerId
     *
     * @return $this
     */
    public function onGet($customerId)
    {
        $this['customerId'] = 'pj123';
        $this['name'] = 'Peter Joseph';

        return $this;
    }
```

#### Order Item Resource

```php
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
     *
     * @return $this
     */
    public function onPost($customerId)
    {
        // do something...
        return $this;
    }
```

#### Response

```json
{
    "class": [
        "order"
    ],
    "properties": {
        "orderNumber": 42,
        "itemCount": 3,
        "status": "pending"
    },
    "entities": [
        {
            "href": "/customer?customerId=pj123",
            "rel": [
                "customer"
            ],
            "class": [
                "info",
                "customer"
            ],
            "properties": {
                "customerId": "pj123",
                "name": "Peter Joseph"
            }
        },
        {
            "href": "/orderitems?orderNumber=42",
            "rel": [
                "order-items"
            ],
            "class": [
                "items",
                "collection"
            ]
        }
    ],
    "actions": [
        {
            "name": "add-item",
            "href": "/orderitems?orderNumber=42",
            "method": "POST",
            "title": "Add Item",
            "type": "application/x-www-form-urlencoded",
            "fields": [
                {
                    "name": "orderNumber",
                    "type": "hidden",
                    "value": "42"
                },
                {
                    "name": "productCode",
                    "type": "text"
                },
                {
                    "name": "quantity",
                    "type": "number"
                }
            ]
        }
    ],
    "links": [
        {
            "rel": [
                "self"
            ],
            "href": "/orders?orderNumber=42"
        },
        {
            "rel": [
                "previous"
            ],
            "href": "/orders?orderNumber=41"
        },
        {
            "rel": [
                "next"
            ],
            "href": "/orders?orderNumber=43"
        }
    ]
}
```


