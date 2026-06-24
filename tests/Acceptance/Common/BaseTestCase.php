<?php

declare(strict_types=1);

namespace Cycle\Tests\Acceptance\Common;

use Cycle\ActiveRecord\Facade;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;

/**
 * Shared helpers for the ActiveRecord acceptance tests.
 *
 * The whole database lifecycle — opening connections, building a clean schema, seeding data, binding
 * the ORM to the {@see Facade} and tearing everything down — is handled by
 * {@see \Cycle\Tests\Acceptance\Testo\DatabaseInterceptor} (registered through the
 * {@see \Cycle\Tests\Acceptance\Testo\DatabasePlugin}). The concrete subclass only declares which
 * driver it targets via a `#[Group('driver-*')]` attribute; everything below simply reads the ORM and
 * DBAL back from the Facade the plugin has already set up.
 */
abstract class BaseTestCase
{
    protected function database(string $name = 'default'): DatabaseInterface
    {
        return Facade::getDatabaseManager()->database($name);
    }

    protected function orm(): ORMInterface
    {
        return Facade::getOrm();
    }

    /**
     * Build a fresh Select for the given role, optionally clearing the identity map first so the
     * query reads straight from the database instead of returning heap-cached instances.
     */
    protected function selectEntity(string $role, bool $cleanHeap = false): Select
    {
        $orm = $this->orm();

        if ($cleanHeap) {
            $orm->getHeap()->clean();
        }

        return new Select($orm, $role);
    }
}
