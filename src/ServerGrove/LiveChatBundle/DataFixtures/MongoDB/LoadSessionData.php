<?php

namespace ServerGrove\LiveChatBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use ServerGrove\LiveChatBundle\Document\Session;
use ServerGrove\LiveChatBundle\Document\Message;

/**
 * Class
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class LoadSessionData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var $visit \ServerGrove\LiveChatBundle\Document\Visit */
        $visit = $manager->merge($this->getReference('visit1'));

        $session = new Session();
        $session->setQuestion('Can I test this Live Chat?');
        $session->setVisit($visit);
        $session->setVisitor($visitor = $visit->getVisitor());
        $session->setRemoteAddr($visit->getRemoteAddr());
        $session->setStatusId(Session::STATUS_IN_PROGRESS);
        $session->setOperator($operator = $manager->getRepository('ServerGroveLiveChatBundle:Operator')->findOneBy(array('email' => 'john@example.com')));

        $manager->persist($session);

        $session->addChatMessage('Hello', $operator);
        $session->addChatMessage('Hi', $visitor);
        $session->addChatMessage('How can I help you?', $operator);
        $session->addChatMessage('Nevermind, good bye!', $visitor);
        $manager->persist($session);

        /** @var $visit \ServerGrove\LiveChatBundle\Document\Visit */
        $visit = $manager->merge($this->getReference('visit2'));

        $session = new Session();
        $session->setQuestion('Is this livechat open source?');
        $session->setVisit($visit);
        $session->setVisitor($visit->getVisitor());
        $session->setRemoteAddr($visit->getRemoteAddr());
        $session->setStatusId(Session::STATUS_WAITING);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 2;
    }
}
