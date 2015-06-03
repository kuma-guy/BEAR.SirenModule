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
      * @Class(name="order")
      * @Action(name="add-item", title="Add Item", method="POST", href="self")
      * @Action(name="delete-item", title="Delete Item", method="DELETE", href="self")
      * @Link(rel="previous", parameter="{orderNumber}")
      * @Link(rel="next", parameter="{orderNumber}")
      */
     public function onGet($orderNumber)
     {
         // This body is going to be property values.
         $this['itemCount'] = 3;
         $this['status'] = "pending";
         return $this;
     }

     /**
      * Add Item
      */
     public function onPost()
     {
     }

     /**
      * Delete Item
      */
     public function onDelete($orderNumber)
     {
     }
}
```



