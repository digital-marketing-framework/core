<?php

namespace DigitalMarketingFramework\Core\Registry;

class ProxyArgument
{
    /** @var callable $fnc */
    protected $fnc;

    public function __construct(
        callable $fnc
    ) {
        $this->fnc = $fnc;
    }

    public function get(): mixed
    {
        return ($this->fnc)();
    }

    public function __invoke(): mixed
    {
        return $this->get();
    }
}
