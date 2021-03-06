<?php

namespace Amp\Postgres;

use Amp\CallableMaker;
use Amp\Iterator;
use Amp\Promise;

class Listener implements Iterator, Operation {
    use CallableMaker, Internal\Operation;

    /** @var \Amp\Iterator */
    private $iterator;

    /** @var string */
    private $channel;

    /** @var callable */
    private $unlisten;

    /**
     * @param \Amp\Iterator $iterator Iterator emitting notificatons on the channel.
     * @param string $channel Channel name.
     * @param callable(string $channel): \Amp\Promise $unlisten Function invoked to unlisten from the channel.
     */
    public function __construct(Iterator $iterator, string $channel, callable $unlisten) {
        $this->iterator = $iterator;
        $this->channel = $channel;
        $this->unlisten = $unlisten;
    }

    /**
     * {@inheritdoc}
     */
    public function advance(): Promise {
        return $this->iterator->advance();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Amp\Postgres\Notification
     */
    public function getCurrent(): Notification {
        return $this->iterator->getCurrent();
    }

    /**
     * @return string Channel name.
     */
    public function getChannel(): string {
        return $this->channel;
    }

    /**
     * Unlistens from the channel. No more values will be emitted from this listener.
     *
     * @return \Amp\Promise<\Amp\Postgres\CommandResult>
     */
    public function unlisten(): Promise {
        /** @var \Amp\Promise $promise */
        $promise = ($this->unlisten)($this->channel);
        $promise->onResolve($this->callableFromInstanceMethod("complete"));
        return $promise;
    }
}
