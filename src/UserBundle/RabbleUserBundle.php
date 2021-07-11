<?php

namespace Rabble\UserBundle;

use Rabble\UserBundle\DependencyInjection\Compiler\TaskPass;
use Rabble\UserBundle\DependencyInjection\Compiler\UserActivityTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RabbleUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TaskPass());
        $container->addCompilerPass(new UserActivityTypePass());
    }
}
