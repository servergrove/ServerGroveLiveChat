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
        $this->createOperator($manager, 'John Doe', 'john@example.com', 'testing');
        $this->createOperator($manager, 'Jane Doe', 'jane@example.com', 'testing');
        $this->createOperator($manager, 'Ismael Ambrosi', 'ismael@servergrove.com', 'testing');

        $manager->flush();
    }

    private function createOperator($manager, $name, $email, $passwd)
    {
        $admin = new Administrator();
        $admin->setName($name);
        $admin->setEmail($email);
        $admin->setPasswd($passwd);

        $manager->persist($admin);
    }
}
