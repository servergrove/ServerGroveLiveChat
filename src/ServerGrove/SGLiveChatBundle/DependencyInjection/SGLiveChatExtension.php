<?php

namespace ServerGrove\SGLiveChatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of SGLiveChatExtension
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class SGLiveChatExtension extends Extension
{

    public function configLoad(array $config, ContainerBuilder $container)
    {
        $this->loadDefaults($config, $container);
    }

    /**
     * Loads the default configuration.
     *
     * @param array $config An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function loadDefaults(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__ . '/../Resources/config');
        $loader->load('livechat.xml');

        // Allow these application configuration options to override the defaults
        $options = array('cache_engine');
        foreach ($options as $key) {
            if (isset($config[$key])) {
                $container->setParameter('livechat.' . $key, $config[$key]);
            }

            $nKey = str_replace('_', '-', $key);
            if (isset($config[$nKey])) {
                $container->setParameter('livechat.' . $key, $config[$nKey]);
            }
        }

        $engine = $container->getParameter('livechat.cache_engine');

        $container->setAlias('livechat.cache.default_engine', 'livechat.cache.engine.' . $engine);

    }

    public function getAlias()
    {
        return 'sglivechat';
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