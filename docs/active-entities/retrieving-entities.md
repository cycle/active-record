# Retrieving Entities

Once you have created [your first entities](defining-entities.md) and [configured](../general/installation.md) one of the adapter packages, you can begin retrieving entities from the database.

Entities that extends `ActiveRecord` class includes powerful Cycle-ORM Query Builder allowing you to fluently query the database table associated with the Entity.

Here is example with `findAll()` method, which will retrieve all of the records from the entity associated database table:

{% tabs %}
{% tab title="Strict" %}
Example with private properties and public getter methods. The `findAll()` method returns all the records from the `users` table, and we can access the `name` property using the `name()` method.

```php
<?php

use App\Entities\User;
 
$users = User::findAll();
 
foreach ($users as $user) {
    echo $user->name();
}
```
{% endtab %}

{% tab title="Fluent" %}
If you are using the fluent approach with public properties, you can directly access the properties:

```php
<?php

use App\Entities\User;
 
$users = User::findAll();
 
foreach ($users as $user) {
    echo $user->name;
}
```
{% endtab %}
{% endtabs %}

### 🛠️ Building Queries

The ActiveRecord `findAll()` method will return all of the results in the entity table. However, since each ActiveRecord Entity includes a [query builder](https://cycle-orm.dev/docs/query-builder-basic/current/en), you may add additional constraints using `query()` method and then invoke the `fetchAll()` method to retrieve the results:

```php
<?php

use App\Entities\User;

$users = User::query()
    ->where('active', 1)
    ->orderBy('name')
    ->fetchAll();
```

In this example, we use the `query()` method to start building a query.

{% hint style="info" %}
Since ActiveRecord Entities includes access to query builder, you should review all of the methods provided by Cycle [query builder](https://cycle-orm.dev/docs/query-builder-basic/current/en). You may use any of these methods when writing your queries.
{% endhint %}

{% hint style="info" %}
For advanced usage of the query builder, this package provides a way to group your queries into separate Query classes by creating classes that extend the [`ActiveQuery`](../active-queries/query-classes.md) class.
{% endhint %}

### 🗂️ Collections

When working with Active Record entities, methods like `findAll()` retrieve multiple records from the database. However, instead of returning a plain PHP array, an instance of a collection class is returned. The specific type of collection class depends on the adapter package being used, such as:

* `spiral/cycle-bridge`,
* `yiisoft/yii-cycle`
* `wayofdev/laravel-cycle-orm-adapter`



