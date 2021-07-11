<?php

namespace Rabble\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

class UserActivityTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $activityTypesDef = $container->findDefinition('rabble_user.user_activity.activity_types');
        foreach ($container->findTaggedServiceIds('rabble_user.user_activity_type') as $id => $tags) {
            $activityTypesDef->addMethodCall('set', [new Expression("service('".addcslashes($id, '\\')."').getName()"), new Reference($id)]);
        }
    }
}
