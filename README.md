# BEAR.SirenModule

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/shingo-kumagai/BEAR.SirenModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/shingo-kumagai/BEAR.SirenModule/?branch=master)
[![Build Status](https://travis-ci.org/shingo-kumagai/BEAR.SirenModule.svg?branch=master)](https://travis-ci.org/shingo-kumagai/BEAR.SirenModule)

Siren support for BEAR.Sunday

**Siren renderer**

It overrides default renderer and represents your resource in Siren format.

Siren [https://github.com/kevinswiber/siren]

## Entity

#### class (optional)

You can specify this optional value with `@SirenClass` annotation.

```
@SirenClass("order")
```

#### properties (optional)

Properties are the response body of the resource object.

## Entities

Add sub related resource entities using `@SirenEmbedResource` annotation.

```
@SirenEmbedResource(rel="customer", src="app://self/customer?customerId={customerId}")
```

And then, you can embed the entity by request like below.

```
$this['customer']->addQuery(['customerId' => $customerId])->eager->request();
```

For sub related link entity use `@SirenEmbedLink` annotation.

```
@SirenEmbedLink(rel="order-items", src="app://self/orderitems?orderNumber={orderNumber}")
```

## Actions

Action can be added using `@SirenAction` annotation.

```
@SirenAction(src="app://self/orderitems?orderNumber={orderNumber}", method="post")
```

The actual method defined as `SirenAction` has to be annotated like below.

```php
    /**
     * @SirenName("add-item")
     * @SirenTitle("Add Item")
     * @SirenField(name="orderNumber", type="hidden", value="{orderNumber}")
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
```

#### name (required)

You need to define action name using `@SirenName` annotation when you want to represent `Action`

#### title (optional)

This is optional. You can specify with `@SirenTitle` annotation.

#### field (optional)

This is going to be controls of the action.
You can add user control for the action with `@SirenField` annotation.

#### type (optional)

WIP

## Links

```
@SirenLink(rel="previous", param="orderNumber")
@SirenLink(rel="next", param="orderNumber")
```

## Example

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


