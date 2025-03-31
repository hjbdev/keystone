<?php

namespace App\Data\Deployments;

class Step
{
    public string $script;

    public function __construct(
        public string $name = 'Step',
        string|callable $script = 'echo "Incomplete Step"',
    ) {
        if (is_callable($script)) {
            $this->script = $script();
        } else {
            $this->script = $script;
        }
    }
}