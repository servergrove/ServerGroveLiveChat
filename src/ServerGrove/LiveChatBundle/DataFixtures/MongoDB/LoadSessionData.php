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
        $this->createClosedSession($manager);
        $this->createWaitingSession($manager);

        $manager->flush();
    }

    private function createWaitingSession($manager)
    {
        /** @var $visit \ServerGrove\LiveChatBundle\Document\Visit */
        $visit = $manager->merge($this->getReference('visit2'));

        $session = new Session();
        $session->setQuestion('Is this livechat open source?');
        $session->setVisit($visit);
        $session->setVisitor($visit->getVisitor());
        $session->setRemoteAddr($visit->getRemoteAddr());
        $session->setStatusId(Session::STATUS_WAITING);
        $manager->persist($session);
    }

    private function createClosedSession($manager)
    {
        /** @var $visit \ServerGrove\LiveChatBundle\Document\Visit */
        $visit = $manager->merge($this->getReference('visit1'));

        $session = new Session();
        $session->setQuestion('Can I test this Live Chat?');
        $session->setVisit($visit);
        $session->setVisitor($visitor = $visit->getVisitor());
        $session->setRemoteAddr($visit->getRemoteAddr());
        $session->setStatusId(Session::STATUS_CLOSED);
        $session->setOperator($operator = $manager->getRepository('ServerGroveLiveChatBundle:Operator')->findOneBy(array('email' => 'john@example.com')));
        $session->getRating()->setComments('Very good chat!');
        $session->getRating()->setGrade(5);

        $manager->persist($session->getRating());
        $manager->persist($session);

        $session->addChatMessage('Hello', $operator);
        $session->addChatMessage('Hi', $visitor);
        $session->addChatMessage('How can I help you?', $operator);
        $session->addChatMessage('Nevermind, good bye!', $visitor);
        $manager->persist($session);

        return array($visit, $session);
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
