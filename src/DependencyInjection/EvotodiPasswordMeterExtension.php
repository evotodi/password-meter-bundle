<?php

namespace Evotodi\PasswordMeterBundle\DependencyInjection;

use Evotodi\PasswordMeterBundle\Models\Requirements;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EvotodiPasswordMeterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('evotodi_password_meter.password_meter');
        if (null !== $config['requirements_provider']) {
            $container->setAlias('evotodi_password_meter.requirements_provider', $config['requirements_provider']);
        }

        if (null !== $config['score_provider']) {
            $container->setAlias('evotodi_password_meter.score_provider', $config['score_provider']);
        }
    }

    public function getAlias(): string
    {
        return 'evotodi_password_meter';
    }


}