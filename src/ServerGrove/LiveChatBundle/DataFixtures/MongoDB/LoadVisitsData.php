<?php

namespace ServerGrove\LiveChatBundle\DataFixtures\MongoDB;

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
        $urls = array(
            'http://sglivechat.local/sglivechat',
            'http://servergrove.com',
            'http://servergrove.es',
            'https://secure.servergrove.com/clients/clientarea.php',
            'https://secure.servergrove.com/clients/knowledgebase.php',
            'http://control.serverogrove.com'
        );
        $agents = array(
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.77 Safari/535.7',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.12 Safari/535.11',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.8 (KHTML, like Gecko) Chrome/17.0.940.0 Safari/535.8',
            'Mozilla/5.0 (X11; CrOS i686 1193.158.0) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7',
            'Mozilla/5.0 (Windows NT 6.0; WOW64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7',
            'Mozilla/6.0 (Macintosh; I; Intel Mac OS X 11_7_9; de-LI; rv:1.9b4) Gecko/2012010317 Firefox/10.0a4',
            'Mozilla/5.0 (Macintosh; I; Intel Mac OS X 11_7_9; de-LI; rv:1.9b4) Gecko/2012010317 Firefox/10.0a4',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:9.0a2) Gecko/20111101 Firefox/9.0a2',
            'Mozilla/5.0 (Windows NT 6.2; rv:9.0.1) Gecko/20100101 Firefox/9.0.1',
            'Mozilla/5.0 (compatible; Google Desktop/5.9.1005.12335; http://desktop.google.com/)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; BTRS26718; GTB7.2; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27'
        );

        $visits = array();
        for ($i = 1; $i < 100; $i++) {
            $visits[] = $this->createVisit($manager, $urls[array_rand($urls)], $agents[array_rand($agents)], long2ip(rand(1111111111, 9999999999)));
        }

        $this->addReference('visit1', $visits[array_rand($visits)]);
        $this->addReference('visit2', $visits[array_rand($visits)]);

        $manager->flush();
    }

    private function createVisit(ObjectManager $manager, $url, $agent, $ip)
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
