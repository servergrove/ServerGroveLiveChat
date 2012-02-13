<?php

namespace ServerGrove\LiveChatBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ServerGrove\LiveChatBundle\Document\Administrator;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Document\OperatorDepartment;

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
        $department = new OperatorDepartment();
        $department->setIsActive(true);
        $department->setName('Staff');
        $manager->persist($department);
        $manager->flush();

        $this->createAdministrator($manager, 'Administrator', 'admin@example.com', 'testing', $department);
        $this->createOperator($manager, 'John Doe', 'john@example.com', 'testing', $department);
        $this->createOperator($manager, 'Jane Doe', 'jane@example.com', 'testing', $department);
        $this->createOperator($manager, 'Ismael Ambrosi', 'ismael@servergrove.com', 'testing', $department);

        $manager->flush();
    }

    private function createOperator(ObjectManager $manager, $name, $email, $passwd, OperatorDepartment $department)
    {
        return $this->saveUser($manager, new Operator(), $name, $email, $passwd, $department);
    }

    private function createAdministrator(ObjectManager $manager, $name, $email, $passwd, OperatorDepartment $department)
    {
        return $this->saveUser($manager, new Administrator(), $name, $email, $passwd, $department);
    }

    private function saveUser(ObjectManager $manager, Operator $operator, $name, $email, $passwd, OperatorDepartment $department)
    {
        $operator->setName($name);
        $operator->setEmail($email);
        $operator->addDepartment($department);

        $encoder = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
        $operator->setPasswd($encoder->encodePassword($passwd, $operator->getSalt()));

        $manager->persist($operator);
    }
}
