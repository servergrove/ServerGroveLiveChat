<?php

namespace Application\ChatBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Description of ChatBundle
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatBundle extends Bundle
{

    public function registerExtensions(ContainerBuilder $container)
    {
        // will register the HelloBundle extension(s) found in DependencyInjection/ directory
        parent::registerExtensions($container);

        // load some defaults
        $container->loadFromExtension('chat', 'config', array(/* your default config for the hello.config namespace */));
    }

}