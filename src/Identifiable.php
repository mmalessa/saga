<?php

declare(strict_types=1);

namespace Mmalessa\Saga;

interface Identifiable
{
    public function getId(): string;
}
