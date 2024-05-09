# Defining Entities

To work with the Cycle Active Record, you need to define entities that extend the `ActiveRecord` class. There are two common approaches to defining entities: the `strict` approach and the `fluent` approach.

These approaches will be used as main examples across documentation to demonstrate various aspects of the library.

{% tabs %}
{% tab title="Strict" %}
**Strict Approach:**

In the strict approach, you define your entity with private properties and provide public getter and setter methods to access and modify the properties. This approach encapsulates the entity's internal state and provides better control over how the properties are accessed and modified.

```php
<?php

declare(strict_types=1);

namespace App\Entities;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'users')]
class User extends ActiveRecord
{
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    private int $id;

    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'string', unique: true)]
    private string $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }
}
```
{% endtab %}

{% tab title="Fluent " %}
**Fluent Approach:**

In the fluent approach, you define your entity with public properties, allowing direct access to the properties without the need for explicit getter and setter methods. This approach leads to more concise and readable code, especially when dealing with simple entities.

```php
<?php

declare(strict_types=1);

namespace App\Entities;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'users')]
class User extends ActiveRecord
{
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    public int $id;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'string', unique: true)]
    public string $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
}
```
{% endtab %}
{% endtabs %}

