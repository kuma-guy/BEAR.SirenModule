# BEAR.Siren

Siren support for BEAR.Sunday

**Siren renderer**

It overrides default renderer and represents your resource in Siren format.

Siren [https://github.com/kevinswiber/siren]

## Entity

#### class (optional)

You can specify this optional value with `@Class` annotation.

```
@SirenClass(name="order")
```

#### properties (optional)

Properties are the response body of the resource object.

## Entities

Add sub related entity resources using `@EmbedResource` annotation.

```
@EmbedResource(rel="customer", src="app://self/customer{?customerId}")
```

And then, you can embed the entity by request like below.

```
$this['customer']->addQuery(['customerId' => $customerId])->eager->request();
```

For sub related link entity use `@EmbedLink` annotation.


```
@EmbedLink(rel="order-items", src="app://self/orderitem{?orderNumber}")
```

#### class (optional)

WIP

#### type (optional)

WIP

## Actions

Action can be added using `@Action` annotation.

```
@Action(src="app://self/orderitem{?orderNumber}", method="post")
```

As a example, the actual method has to be annotated like below

```
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
```



#### name (required)

You need to define action name using `@Name` annotation when you want to represent `Action`

#### title (optional)

This is optional. You can specify with `@Title` annotation.

#### field (optional)

This is going to be controls of the action.
You can add user control for the action with `@Field` annotation.


## Links

```
@Link(rel="previous", parameter="{orderNumber}")
@Link(rel="next", parameter="{orderNumber}")
```

#### rel (required)
#### href (optional)

## Example

#### Order Resource

```php
class Order extends ResourceObject
{
    /**
     * @EmbedResource(rel="customer", src="app://self/customer{?customerId}")
     * @EmbedLink(rel="order-items", src="app://self/orderitem{?orderNumber}")
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

        return $this;
    }
}
```

#### Response

```json
{
    "class": [
        "orders"
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
            "properties": {
                "customerId": "pj123",
                "name": "Peter Joseph"
            }
        },
        {
            "href": "/orderitem?orderNumber=42",
            "rel": [
                "order-items"
            ]
        }
    ],
    "actions": [
        {
            "name": "add-item",
            "href": "/orderitem?orderNumber=42",
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
        }
    ]
}
```


