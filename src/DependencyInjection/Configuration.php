<?php

namespace Evotodi\PasswordMeterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder('evotodi_password_meter');
    }

//    public function getConfigTreeBuilder(): TreeBuilder
//    {
//        $treeBuilder = new TreeBuilder('evotodi_password_meter');
//		$rootNode = $treeBuilder->getRootNode();
//
//        $rootNode
//            ->children()
//                ->integerNode('min_length')->info('Minimum password length')->defaultValue(5)->min(0)->end()
//                ->integerNode('max_length')->info('Maximum password length')->defaultNull()->end()
//            ->end()
//            ->validate()
//            ->ifTrue(function ($v){
//                if (!is_null($v['max_length'])) {
//                    return $v['min_length'] >= $v['max_length'];
//                }
//            })
//            ->thenInvalid('"min_length" must be less than "max_length"')->end()
//        ;
//
//        return $treeBuilder;
//    }

}