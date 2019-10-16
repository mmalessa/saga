<?php

declare(strict_types=1);

namespace Mmalessa\Saga\Tests;

use Mmalessa\Saga\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    /** @dataProvider data_for_it_creates_state */
    public function test_it_creates_state($sagaId, $payload, $done)
    {
        $state = new State($sagaId);
        foreach ($payload as $k => $v) {
            $state->set($k, $v);
        }
        if ($done) {
            $state->setDone();
        }

        $this->assertEquals($sagaId, $state->getSagaId());
        foreach ($payload as $k => $v) {
            $this->assertEquals($v, $state->get($k));
        }
        $this->assertEquals($done, $state->isDone());
    }

    public function data_for_it_creates_state()
    {
        return [
            ['6ec9c449-206c-47a2-ba2b-860a678ecbe9', ['key1' => 'value1', 'key2' => 'value2'], false],
            ['6ec9c449-206c-47a2-ba2b-860a678ecbe9', ['key1' => 'value1', 'key2' => 'value2'], true],
            ['6ec9c449-206c-47a2-ba2b-860a678ecbe9', [], false],
        ];
    }
}
