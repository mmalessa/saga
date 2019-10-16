# Saga
Saga pattern simple implementation in Symfony 4.3+  
Development vertion.
Use it at your own risk.

# Install
**Temporary solution - until the code has stabilized.**

Clone it into ../mmalessa/saga ...or wherever you want.
```shell script
git clone...
```

composer.json - add:
```json
"repositories": [
        {
            "type": "path",
            "url": "../mmalessa/saga"
        }
    ]
```

```shell script
composer req mmalessa/saga
```

Symfony services.yaml - add something like this:
```yaml
services:
    app.saga.state_repository:
        class: 'Mmalessa\Saga\InMemoryStateRepository'

    App\Saga\MySaga:
        arguments:
            $stateRepository: '@app.saga.state_repository'
            $type: 'mysaga'
        tags: ['kernel.event_subscriber']
```

# Usage

```php
use Symfony\Component\EventDispatcher\EventDispatcher;

class X {
    [...]
    method() {
        [...]
        $this->eventDispatcher->dispatch(new SomethingHappened('a7933354-d489-4522-bd59-111a985cbf7c', new \DateTime(), 'My description'));
        $this->eventDispatcher->dispatch(new SomethingElseHappened('a7933354-d489-4522-bd59-111a985cbf7c', new \DateTime(), 'My second description'));
        [...]
    }
}
```

```php
use App\Command\DoSomething;
use App\Event\SomethingElseHappened;
use App\Event\SomethingHappened;
use Broadway\CommandHandling\CommandBus;
use Mmalessa\Saga\Saga;
use Mmalessa\Saga\State;
use Mmalessa\Saga\StateRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MySaga extends Saga implements EventSubscriberInterface
{
    const EVENTS = [
        SomethingHappened::class => ['somethingHappened', 1],
        SomethingElseHappened::class => ['somethingElseHappened', 1]
    ];

    private $commandBus;

    public function __construct(StateRepository $stateRepository, string $type, CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        parent::__construct($stateRepository, $type);
    }

    public function somethingHappened(SomethingHappened $event, State $state): State
    {
        $state->set('id', $event->getId());
        $state->set('something', 'OK');
        $this->tryToDoSomething($state);
        return $state;
    }
    public function somethingElseHappened(SomethingElseHappened $event, State $state): State
    {
        $state->set('id', $event->getId());
        $state->set('somethingElse', 'OK');
        $this->tryToDoSomething($state);
        return $state;
    }

    public function tryToDoSomething(State &$state)
    {
        print_r($state); echo PHP_EOL;
        $condition1 = $state->get('something') === 'OK';
        $condition2 = $state->get('somethingElse') === 'OK';
        if ($condition1 && $condition2) {
            $state->setDone();
            $this->commandBus->dispatch(new DoSomething((string)$state->get('id')));
        }
    }
}
```

```php
use Mmalessa\Saga\Identifiable;

class SomethingHappened implements Identifiable
{
    private $id;
    private $dateTime;
    private $description;

    public function __construct(string $Id, \DateTime $dateTime, string $description)
    {
        $this->id = $Id;
        $this->dateTime = $dateTime;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
```
