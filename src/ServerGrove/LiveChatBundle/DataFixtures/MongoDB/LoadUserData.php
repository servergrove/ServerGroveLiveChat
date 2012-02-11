<?php

namespace ServerGrove\LiveChatBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ServerGrove\LiveChatBundle\Document\Administrator;
use ServerGrove\LiveChatBundle\Document\Operator;

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
        $this->createAdministrator($manager, 'Administrator', 'admin@example.com', 'testing');
        $this->createOperator($manager, 'John Doe', 'john@example.com', 'testing');
        $this->createOperator($manager, 'Jane Doe', 'jane@example.com', 'testing');
        $this->createOperator($manager, 'Ismael Ambrosi', 'ismael@servergrove.com', 'testing');

        $manager->flush();
    }

    private function createOperator(ObjectManager $manager, $name, $email, $passwd)
    {
        return $this->saveUser($manager, new Operator(), $name, $email, $passwd);
    }

    private function createAdministrator(ObjectManager $manager, $name, $email, $passwd)
    {
        return $this->saveUser($manager, new Administrator(), $name, $email, $passwd);
    }

    private function saveUser(ObjectManager $manager, Operator $operator, $name, $email, $passwd)
    {
        $operator->setName($name);
        $operator->setEmail($email);

        $encoder = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
        $operator->setPasswd($encoder->encodePassword($passwd, $operator->getSalt()));

        $manager->persist($operator);
    }
}
