<?php

declare(strict_types=1);

namespace Mmalessa\Saga;

class State
{
    private $sagaId;
    private $payload;
    private $done;

    public function __construct(string $sagaId)
    {
        if ($sagaId === '') {
            throw new StateException("SagaId cannot be empty.");
        }
        $this->sagaId = $sagaId;
        $this->payload = [];
        $this->done = false;
    }

    public function getSagaId(): string
    {
        return $this->sagaId;
    }

    public function set(string $key, $value)
    {
        $this->payload[$key] = $value;
    }

    public function get(string $key)
    {
        if (!array_key_exists($key, $this->payload)) {
            return null;
        }
        return $this->payload[$key];
    }

    public function setDone()
    {
        $this->done = true;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function serialize(): array
    {
        return [
            'sagaId' => $this->sagaId,
            'payload' => $this->payload,
            'done' => $this->done,
        ];
    }

    public function deserialize(array $data): self
    {
        $state = new self($data['sagaId']);
        $state->payload = $data['payload'];
        $state->done = $data['done'];
        return $state;
    }
}
