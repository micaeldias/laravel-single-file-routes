<?php

namespace MicaelDias\SingleFileRoutes\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    public function __construct(
        public string $method,
        public string $uri,
        public ?string $name = null,
        public ?string $group = null,
        public array $middleware = []
    ) {

    }
}
