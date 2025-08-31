<?php

declare(strict_types=1);

namespace Cycle\ActiveRecord\Internal;

use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Transaction\StateInterface;
use Cycle\ORM\Transaction\UnitOfWork;

/**
 * Active Record Entity Manager.
 *
 * @internal
 */
final class EntityManager implements EntityManagerInterface
{
    private ?UnitOfWork $uow = null;

    /**
     * @param \Closure(): UnitOfWork $factory
     */
    public function __construct(
        private readonly \Closure $factory,
        private readonly bool $autoExecute = false,
    ) {}

    public function persistState(object $entity, bool $cascade = true): EntityManagerInterface
    {
        $this->getUow()->persistState($entity, $cascade);
        $this->autoExecute === true and $this->run();
        return $this;
    }

    public function persist(object $entity, bool $cascade = true): EntityManagerInterface
    {
        $this->getUow()->persistDeferred($entity, $cascade);
        $this->autoExecute === true and $this->run();
        return $this;
    }

    public function delete(object $entity, bool $cascade = true): EntityManagerInterface
    {
        $this->getUow()->delete($entity, $cascade);
        $this->autoExecute === true and $this->run();
        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function run(): StateInterface
    {
        if ($this->uow === null) {
            return new EmptyState();
        }

        $state = $this->uow->run();
        $this->clean();
        $state->isSuccess() or throw $state->getLastError();
        return $state;
    }

    public function clean(): static
    {
        $this->uow = null;
        return $this;
    }

    private function getUow(): UnitOfWork
    {
        return $this->uow ??= ($this->factory)();
    }
}
