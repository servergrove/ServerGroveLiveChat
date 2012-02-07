<?php

namespace ServerGrove\LiveChatBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ServerGrove\LiveChatBundle\Document\Administrator;

/**
 * Class LoadUserData
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class LoadUserData implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new Administrator();
        $admin->setName('John Doe');
        $admin->setEmail('john@example.com');
        $admin->setPasswd('testing');

        $manager->persist($admin);
        $manager->flush();
    }
}
