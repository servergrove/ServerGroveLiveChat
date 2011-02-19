<?php

namespace Application\ChatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of ChatExtension
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatExtension extends Extension
{

    public function configLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__ . '/../Resources/config');
        $loader->load('livechat.xml');
    }

    public function getAlias()
    {
        return 'chat';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getXsdValidationBasePath()
    {
        return null;
    }

}