# BEAR.SirenRenderer

This is Siren renderer for BEAR.Sunday
It overrides default renderer and represents your resource in Siren format.

Siren
https://github.com/kevinswiber/siren

## Entity

### class (Optional)

You can specify this value with @class annotation. Otherwise it automatically defined as the class name of the resource object.

@Class("order")

### properties

By default, This is the body of the resource object.
However, you can add this value with @Properties annotation.

@Properties(additionalParameter=42)

## Entities

@Entity(class="items", rel="app://self/order-items")

### class (Optional)

### rel (Required)

### href (Required)

### type (Optional)

## Actions

Action show available behaviors that your resource supports.

@Action(name="add-item", title="Add Item", method="POST", href="self")

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

```php
class Order extends ResourceObject
{
    /**
     * @Name("get-item")
     * @Title("Get Item")
     *
     * @Embed(rel="customer", src="app://self/customer{?customerId}")
     * @Embed(rel="order-items", src="app://self/orderitem{?orderNumber}")
     *
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


```json
{
    "class": [ "order" ],
    "properties": {
        "orderNumber": 42,
        "itemCount": 3,
        "status": "pending"
    },
    "entities": [
        {
            "href": "app://self/customer{?customerId}",
            "rel": [ "customer" ],
            "properties": {
                "customerId": "pj123",
                "name": "Peter Joseph"
            }
        },
        {
            "href": "app://self/orderitem{?orderNumber}",
            "rel": [ "order-items" ]
        }
    ],
    "links": [
        { "rel": [ "self" ], "href": "http://localhost/order?orderNumber=42" }
    ]
}
```


