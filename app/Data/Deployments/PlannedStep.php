<?php

namespace App\Data\Deployments;

class PlannedStep
{
    protected string $script;

    public function __construct(
        public string $name = 'Step',
        protected array $secrets = [],
        string|callable $script = 'echo "Incomplete Step"',
    ) {
        if (is_callable($script)) {
            $this->script = $script();
        } else {
            $this->script = $script;
        }
    }

    public function getSafeScript(): string
    {
        $script = $this->script;
        foreach ($this->secrets as $key => $value) {
            $script = str_replace("[!{$key}]", '********', $script);
        }

        return $script;
    }

    public function getScript(): string
    {
        $script = $this->script;
        foreach ($this->secrets as $key => $value) {
            $script = str_replace("[!{$key}]", $value, $script);
        }

        return $script;
    }
}
