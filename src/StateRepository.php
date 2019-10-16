<?php

declare(strict_types=1);

namespace Mmalessa\Saga;

interface StateRepository
{
    public function get(string $sagaId, string $type): State;
    public function save(string $sagaId, string $type, State $state): void;
}
