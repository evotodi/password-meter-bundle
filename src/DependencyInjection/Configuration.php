<?php

namespace Evotodi\PasswordMeterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('evotodi_password_meter');
		$rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('requirements_provider')->defaultNull()->info('Custom password requirements provider class')->end()
                ->scalarNode('score_provider')->defaultNull()->info('Custom password score provider class')->end()
            ->end()
        ;

        return $treeBuilder;
    }

}