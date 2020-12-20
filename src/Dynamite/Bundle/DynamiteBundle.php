<?php
declare(strict_types=1);

namespace Dynamite\Bundle;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DynamiteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }

}