<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Controller;

use ServerGrove\SGLiveChatBundle\Document\CannedMessage;
use ServerGrove\SGLiveChatBundle\Document\Operator;
use ServerGrove\SGLiveChatBundle\Document\Operator\Department;
use Symfony\Component\BrowserKit\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Description of AdminControllerTest
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminControllerTest extends WebTestCase
{

    public function testIndex()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/admin/sglivechat');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please enter your e-mail and password to access the admin panel")')->count(), 'HTML not contains Login description');

        # Test Login
        $form = $crawler->selectButton('Login')->form();
        $this->fillLoginFormFields($form);
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirection(), 'Is not redirecting');

        $this->logout($client);

        $this->login($client);

        $client->followRedirects(false);

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/admin/sglivechat');
        $this->assertTrue($client->getResponse()->isRedirect());

        $this->logout($client);

        unset($client, $crawler);
    }

    public function testCannedMessages()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/canned-messages');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Canned Messages list")')->count());

        $this->logout($client);

        unset($client, $crawler);
    }

    public function testEditCannedMessage()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $cannedMessage = new CannedMessage();
        $cannedMessage->setTitle('Canned message title');
        $cannedMessage->setContent('Canned message content');

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $dm->persist($cannedMessage);
        $dm->flush();

        $cannedMessageId = $cannedMessage->getId();
        $dm->detach($cannedMessage);

        unset($cannedMessage);

        $crawler = $client->request('GET', '/admin/sglivechat/canned-message/' . $cannedMessageId);

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit canned message")')->count());


        $form = $crawler->selectButton('Submit')->form();
        $newTitle = $form['cannedmessage[title]'] = 'New Title of canned message';
        $crawler = $client->submit($form);

        $this->assertPostRedirect($client);

        $cannedMessage = $dm->getRepository('SGLiveChatBundle:CannedMessage')->find($cannedMessageId);
        $this->assertEquals($newTitle, $cannedMessage->getTitle());

        $dm->remove($cannedMessage);
        $dm->flush();

        $this->logout($client);

        unset($client, $crawler);
    }

    public function testNewCannedMessage()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/canned-message');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new canned message")')->count());


        $form = $crawler->selectButton('Submit')->form();
        $title = $form['cannedmessage[title]'] = 'Title of canned message';
        $form['cannedmessage[content]'] = 'Content of canned message';
        $crawler = $client->submit($form);

        $this->assertPostRedirect($client);

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $this->assertGreaterThan(0, $dm->getRepository('SGLiveChatBundle:CannedMessage')->findBy(array('title' => $title))->count(), 'Unexistent canned message after form submit');

        $dm->createQueryBuilder('SGLiveChatBundle:CannedMessage')->remove()->field('title')->equals($title)->getQuery()->execute();

        $this->assertEquals(0, $dm->getRepository('SGLiveChatBundle:CannedMessage')->findBy(array('title' => $title))->count(), 'Canned message still exists after removal');

        $dm->flush();

        $this->logout($client);

        unset($client, $crawler);
    }

    public function testOperators()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/operators');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Operators list")')->count());

        $this->logout($client);

        unset($client, $crawler);
    }

    public function testNewOperator()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/operator');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new operator")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $form['operator[name]'] = 'John Doe';
        $form['operator[email][first]'] = $form['operator[email][second]'] = 'john@doe.com';
        $form['operator[passwd][first]'] = $form['operator[passwd][second]'] = 'johnpass';

        $crawler = $client->submit($form);

        $this->assertPostRedirect($client);

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $this->assertGreaterThan(0, $dm->getRepository('SGLiveChatBundle:Operator')->findBy(array('email' => 'john@doe.com'))->count(), 'Unexistent operator after form submit');

        $dm->createQueryBuilder('SGLiveChatBundle:Operator')->remove()->field('email')->equals('john@doe.com')->getQuery()->execute();
        $dm->flush();

        $this->assertEquals(0, $dm->getRepository('SGLiveChatBundle:Operator')->findBy(array('email' => 'john@doe.com'))->count(), 'operator still exists after removal');

        $this->logout($client);

        unset($dm, $client, $crawler);
    }

    public function testEditOperator()
    {
        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();

        $operator = new Operator();
        $operator->setName('John Doe');
        $operator->setEmail('john@doe.com');
        $operator->setPasswd('johnpass');

        $dm->persist($operator);
        $dm->flush();

        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $operatorId = $operator->getId();

        $dm->detach($operator);

        unset($operator);

        $crawler = $client->request('GET', '/admin/sglivechat/operator/' . $operatorId);
        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit operator ")')->count());


        $form = $crawler->selectButton('Submit')->form();

        $form['operator[name]'] = 'John J. Doe';

        $crawler = $client->submit($form);

        $this->assertPostRedirect($client);

        $operator = $dm->getRepository('SGLiveChatBundle:Operator')->find($operatorId);
        $this->assertEquals('John J. Doe', $operator->getName());

        $dm->remove($operator);

        $this->logout($client);

        unset($dm, $operator, $client, $crawler);
    }

    public function testDepartments()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/operator/departments');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Departments list")')->count());

        $this->logout($client);

        unset($client, $crawler);
    }

    public function testNewOperatorDepartment()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/operator/department');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new department")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $form['operatordepartment[name]'] = 'Test Department';

        $crawler = $client->submit($form);

        $this->assertPostRedirect($client);

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $this->assertGreaterThan(0, $dm->getRepository('SGLiveChatBundle:Operator\\Department')->findBy(array('name' => 'Test Department'))->count(), 'Unexistent department after form submit');

        $dm->createQueryBuilder('SGLiveChatBundle:Operator:Department')->remove()->field('name')->equals('Test Department')->getQuery()->execute();
        $dm->flush();

        $this->assertEquals(0, $dm->getRepository('SGLiveChatBundle:Operator:Department')->findBy(array('name' => 'Test Department'))->count(), 'Department still exists after removal');

        $this->logout($client);

        unset($dm, $operator, $client, $crawler);
    }

    public function testEditOperatorDepartment()
    {
        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();

        $department = new Department();
        $department->setName('Test department');

        $dm->persist($department);
        $dm->flush();

        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $departmentId = $department->getId();

        $dm->detach($department);

        unset($department);

        $crawler = $client->request('GET', '/admin/sglivechat/operator/department/' . $departmentId);
        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit department ")')->count());


        $form = $crawler->selectButton('Submit')->form();

        $form['operatordepartment[name]'] = 'My test department';

        $crawler = $client->submit($form);

        $this->assertPostRedirect($client);

        $department = $dm->getRepository('SGLiveChatBundle:Operator\\Department')->find($departmentId);
        $this->assertEquals('My test department', $department->getName());

        $dm->remove($department);

        $this->logout($client);

        unset($dm, $operator, $client, $crawler);
    }

    public function testSessions()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        $this->login($client);

        $crawler = $client->request('GET', '/admin/sglivechat/console/sessions');

        $this->assertGetSuccessful($client);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Current Sessions:")')->count());

        $this->logout($client);

        unset($client, $crawler);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->loadFixtures();
    }

    protected function tearDown()
    {
        $this->unloadFixtures();
        parent::tearDown();
    }

    private function assertGetSuccessful(Client $client)
    {
        $this->assertTrue($client->getResponse()->isOk(), 'GET response not successful. Code: ' . $client->getResponse()->getStatusCode());
    }

    private function assertPostRedirect(Client $client)
    {
        $this->assertEquals('POST', $client->getRequest()->getMethod(), 'Request type is not POST');
        $this->assertTrue($client->getResponse()->isRedirect(), 'POST request is redirecting');
    }

    private function fillLoginFormFields($form)
    {
        $form['operatorlogin[email]'] = 'ismael@servergrove.com';
        $form['operatorlogin[passwd]'] = 'ismapass';
    }

    private function getDocumentManager()
    {
        return $this->getNewKernel()->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }

    private function getNewKernel()
    {
        $kernel = $this->createKernel(array());
        $kernel->boot();

        return $kernel;
    }

    private function loadFixtures()
    {
        $dm = $this->getDocumentManager();

        $operator = new Operator();
        $operator->setName('Ismael Ambrosi');
        $operator->setEmail('ismael@servergrove.com');
        $operator->setPasswd('ismapass');

        $dm->persist($operator);
        $dm->flush();
    }

    private function login(Client $client)
    {
        $crawler = $client->request('GET', '/admin/sglivechat');

        if ($client->getResponse()->isSuccessful()) {
            $form = $crawler->selectButton('Login')->form();
            $this->fillLoginFormFields($form);

            return $client->submit($form);
        }

        return false;
    }

    private function logout(Client $client)
    {
        return $client->request('GET', '/admin/sglivechat/logout');
    }

    private function unloadFixtures()
    {
        $dm = $this->getDocumentManager();

        $operator = $dm->getRepository('SGLiveChatBundle:Operator')->findOneBy(array('email' => 'ismael@servergrove.com'));

        $dm->remove($operator);
        $dm->flush();
    }

}