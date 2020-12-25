<?php
declare(strict_types=1);

namespace Dynamite\Repository;

use Dynamite\AccessPattern;

interface AccessPatternsProviderInterface
{
    /**
     * @return AccessPattern[]
     */
    public function registerAccessPatterns(): array;
}