# BEAR.Siren

Siren support for BEAR.Sunday

**Siren renderer**
It overrides default renderer and represents your resource in Siren format.

Siren [https://github.com/kevinswiber/siren]

## Entity

### class (Optional)

@Class

### properties

Properties are the response body of the resource object.

## Entities

Add sub related entity using @Embed annotation.

```
@Embed(rel="customer", src="app://self/customer{?customerId}")
```

And then, get the entity by request in the method like below.

```
$this['customer']->addQuery(['customerId' => $customerId])->eager->request();
```

### class (Optional)

### type (Optional)

## Actions

Action can be added using @Action annotation.

@Action(src="app://self/orderitem{?orderNumber}", method="post")

### name (Required)

Default name will be the method name on the resource object such as OnGet, OnPost..
You can override the name with annotation @name

### title (Optional)

This is optional value for title of the action.

### method (Optional)

This is optional value for method of the action.

### href (Required)

If you defined as "self", the module automatically build url for your resource.

## Links

@Link(rel="previous", parameter="{orderNumber}")
@Link(rel="next", parameter="{orderNumber}")

### rel (Required)
### href (Optional)

## Example

### Order Resource

```php
class Order extends ResourceObject
{
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
        $this['itemCount']   = 3;
        $this['status']      = "pending";

        $customerId = "pj123";

        $this['customer']->addQuery(['customerId' => $customerId])->eager->request();
        $this['order-items'] = [];

        return $this;
    }
}
```

### Response

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


