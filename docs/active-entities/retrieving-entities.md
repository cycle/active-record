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

The Cycle Active Record team is constantly working on improving the library. Keep an eye on the [official repository](https://github.com/cycle/active-record) for updates and new features, such as the potential introduction of Collection support for query results.

There is currently a Proof of Concept (PoC) Pull Request for introducing Collection support for query results:

* [PR #20: Implement basic collections](https://github.com/cycle/active-record/pull/20)

If you're interested in using Collections with Cycle Active Record, you can:

1. Follow the progress of this PR
2. Contribute to the discussion by providing feedback or use cases
3. Help with the implementation if you have experience with Collections in ORMs

Contributions from the community are welcome and can significantly impact the direction and features of the library. If Collections are a feature you're particularly interested in, consider getting involved in the development process.

Remember to check the contribution guidelines before submitting any code or opening issues.







