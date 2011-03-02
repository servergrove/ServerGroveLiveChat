<?php

namespace ServerGrove\SGLiveChatBundle\Tests;

use ServerGrove\SGLiveChatBundle\SGLiveChatBundle;
use Symfony\Bundle\DoctrineMongoDBBundle\DoctrineMongoDBBundle;
use Symfony\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\ZendBundle\ZendBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Loader\LoaderInterface;

class TestKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new ZendBundle(),
            new SwiftmailerBundle(),
            new DoctrineBundle(),
            new DoctrineMongoDBBundle(),
            new SGLiveChatBundle());

        return $bundles;
    }

    public function registerRootDir()
    {
        return sys_get_temp_dir();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/Resources/config/config.yml');
    }
}