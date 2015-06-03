# BEAR.SirenRenderer

This is Siren renderer for BEAR.Sunday
It overrides default renderer and represents your resource in Siren format.

Siren
https://github.com/kevinswiber/siren

## Entity

### class (Optional.)

You can specify this value with @class annotation. Otherwise it automatically defined as the class name of the resource object.

@class("order")

### properties

You can specify this value with @class annotation.

@properties(orderNumber=42, itemCount=3, status="pending")

## Entities

WIP

## Actions

Action show available behaviors that your resource supports.

@action(name="add-item", title="Add Item", method="POST", href="self")

### name (Required.)

Default name will be the method name on the resource object such as OnGet, OnPost..
You can override the name with annotation @name

### title (Optional.)

This is optional value for title of the action.

### method (Optional.)

This is optional value for method of the action.

### href (Required.)

If you defined as "self", the module automatically build url for your resource.


