# Query Classes

Query classes serve as a wrapper around the query builder provided by CycleORM, allowing for the grouping of common queries together for later reuse and separation.&#x20;

The `ActiveQuery` class extends CycleORM's `Select` class and is specifically designed to integrate with entities managed by `ActiveRecord`. It facilitates the creation of queries that can be easily maintained and reused throughout the application.

### Standard Usage

Entities that extend the `ActiveRecord` class automatically benefit from the `ActiveQuery` capabilities through the `query()` method:

{% code title="ActiveRecord.php" %}
```php
public static function query(): ActiveQuery
{
    return new ActiveQuery(static::class);
}
```
{% endcode %}

This method provides a straightforward way to begin a query operation tailored to the entity's context.

### Defining Custom Query Classes

To encapsulate specific query logic, developers can create custom query classes that extend the `ActiveQuery` class.&#x20;

For instance, the `UserQuery` class might define methods to handle common requirements such as filtering by active status or sorting by creation time:

{% code title="UserQuery.php" %}
```php
class UserQuery extends ActiveQuery
{
    public function active(bool $state = true): UserQuery
    {
        return $this->where(['active' => $state]);
    }

    public function orderByCreatedAt(string $direction = 'ASC'): UserQuery
    {
        return $this->orderBy(['created_at' => $direction]);
    }
}
```
{% endcode %}

By overriding the `query()` method in a derived entity class to return an instance of a custom query class, developers can significantly simplify the data access layer. This approach not only enhances code readability but also improves the organization of business logic:

<pre class="language-php" data-title="User.php"><code class="lang-php"><strong>&#x3C;?php
</strong>
declare(strict_types=1);

namespace Cycle\App\Entity;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\App\Query\UserQuery;

#[Entity(table: 'user')]
class User extends ActiveRecord
{
    // ...

    /**
     * @return UserQuery&#x3C;static>
     */
    public static function query(): UserQuery
    {
        return new UserQuery(static::class);
    }
}
</code></pre>

### Usage Example

Fetch all user records, which are not active and are ordered by `created-at` field in descending order:

```php
<?php

use App\Entities\User;

$users = User::query()
    ->active(false)
    ->orderByCreatedAt('DESC')
    ->fetchAll();
```

### Advantages of Using ActiveQuery

* **Organization**: Groups common queries, enhancing code organization and separation.
* **Reusability**: Promotes the reuse of query logic across different parts of the application.
* **Maintainability**: Simplifies maintenance by localizing query logic within dedicated classes



