<?php

namespace ServerGrove\LiveChatBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * Description of ServerGroveLiveChatExtension
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ServerGroveLiveChatExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('livechat.xml');

        $this->loadCacheEngine($config, $container);
    }

    private function loadCacheEngine(array $config, ContainerBuilder $container)
    {
        $container->setAlias('livechat.cache.default_engine', 'livechat.cache.engine.' . $config['cache_engine']);
    }
}