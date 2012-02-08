<?php

namespace ServerGrove\LiveChatBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use ServerGrove\LiveChatBundle\Document\Visit;
use ServerGrove\LiveChatBundle\Document\Visitor;
use ServerGrove\LiveChatBundle\Document\VisitHit;
use ServerGrove\LiveChatBundle\Document\VisitLink;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Class
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class LoadVisitsData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->createVisitor($manager, 'http://sglivechat.local/sglivechat', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7', '8.8.8.8');
        $visit1 = $this->createVisitor($manager, 'http://servergrove.com', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; BTRS26718; GTB7.2; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)', '192.168.1.1');
        $visit2 = $this->createVisitor($manager, 'http://servergrove.es', 'Mozilla/5.0 (compatible; Google Desktop/5.9.1005.12335; http://desktop.google.com/)', '127.0.0.1');

        $this->addReference('visit1', $visit1);
        $this->addReference('visit2', $visit2);

        $manager->flush();
    }

    private function createVisitor(ObjectManager $manager, $url, $agent, $ip)
    {
        $visitor = new Visitor();
        $visitor->setAgent($agent);
        $visitor->setLanguages('en');
        $visitor->setRemoteAddr($ip);
        $manager->persist($visitor);

        $visit = new Visit();
        $visit->setRemoteAddr($visitor->getRemoteAddr());
        $visit->setVisitor($visitor);
        $visit->setKey(md5($url.$agent.$ip.mt_rand(1, 1000)));
        $visit->setLocalTime(date('r'));
        $visit->setLocalTimeZone(date('O'));
        $visit->registerUpdatedDate();

        $visitLink = new VisitLink();
        $visitLink->setUrl($url);
        $manager->persist($visitLink);

        $visitHit = new VisitHit();
        $visitHit->setVisitLink($visitLink);
        $manager->persist($visitHit);

        $visit->addHit($visitHit);

        $manager->persist($visit);

        return $visit;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 0;
    }
}
