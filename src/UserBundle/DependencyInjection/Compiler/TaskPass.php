<?php

namespace Rabble\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TaskPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taskCollectionDef = $container->findDefinition('rabble_user.security.task_collection');
        foreach ($container->findTaggedServiceIds('rabble.task_bundle') as $id => $tags) {
            $taskCollectionDef->addMethodCall('add', [new Reference($id)]);
        }
    }
}
