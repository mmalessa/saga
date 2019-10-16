<?php

declare(strict_types=1);

namespace Mmalessa\Saga;

abstract class Saga
{
    const EVENTS = [];
    private $stateRepository;
    private $type;

    public static function getSubscribedEvents()
    {
        $events = [];
        foreach (static::EVENTS as $k => $v) {
            $events[$k] = ['handle', $v[1]];
        }
        return $events;
    }

    public function __construct(StateRepository $stateRepository, string $type)
    {
        $this->stateRepository = $stateRepository;
        $this->type = $type;
    }

    public function handle(Identifiable $event)
    {
        $eventName = get_class($event);
        if (!array_key_exists($eventName, static::EVENTS)) {
            return;
        }
        $methodName = static::EVENTS[$eventName][0];
        $state = $this->stateRepository->get($event->getId(), $this->type);
        $newState = $this->{$methodName}($event, $state);
        $this->stateRepository->save($event->getId(), $this->type, $newState);
    }
}
