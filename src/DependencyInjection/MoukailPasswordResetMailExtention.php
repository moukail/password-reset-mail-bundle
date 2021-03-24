<?php

namespace Moukail\PasswordResetMailBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MoukailPasswordResetMailExtention extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.xml');

        //$loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__) .'/Resources/config'));
        //$loader->load('services.yml');

        $container->setParameter('moukail_password_reset.email_base_url', $config['email_base_url']);

        $helperDefinition = $container->getDefinition('moukail_password_reset.reset_password_controller');
        $helperDefinition->replaceArgument(1, new Reference($config['user_repository']));

        $helperDefinition = $container->getDefinition('moukail_password_reset.reset_password_mail_handler');
        $helperDefinition->replaceArgument(2, $config['from_address']);
        $helperDefinition->replaceArgument(3, $config['from_name']);
    }

    public function getAlias()
    {
        return 'moukail_password_reset';
    }
}
