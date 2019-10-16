<?php

declare(strict_types=1);

namespace Mmalessa\Saga;

class InMemoryStateRepository implements StateRepository
{
    private $states = [];

    public function get(string $sagaId, string $type): State
    {
        if (!array_key_exists($type, $this->states) || !array_key_exists($sagaId, $this->states[$type])) {
            return new State($sagaId);
        }
        return $this->states[$type][$sagaId];
    }

    public function save(string $sagaId, string $type, State $state): void
    {
        if ($state->isDone()) {
            unset ($this->states[$type][$sagaId]);
        } else {
            $this->states[$type][$sagaId] = $state;
        }
    }
}
