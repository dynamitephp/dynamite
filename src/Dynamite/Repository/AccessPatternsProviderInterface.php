<?php
declare(strict_types=1);

namespace Dynamite\Repository;


interface AccessPatternsProviderInterface
{
    public function registerAccessPatterns(): array;
}