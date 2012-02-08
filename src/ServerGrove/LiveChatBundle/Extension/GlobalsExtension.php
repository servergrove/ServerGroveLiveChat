<?php

namespace ServerGrove\LiveChatBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GlobalsExtension
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class GlobalsExtension extends \Twig_Extension
{

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sglc_globals';
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array('currentRoute' => $this->container->get('request')->get('_route'));
    }

}
