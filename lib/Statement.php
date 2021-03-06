<?php

namespace Amp\Postgres;

use Amp\Promise;

interface Statement {
    /**
     * @param mixed ...$params
     *
     * @return \Amp\Promise<\Amp\Postgres\CommandResult|\Amp\Postgres\TupleResult>
     */
    public function execute(...$params): Promise;
}
