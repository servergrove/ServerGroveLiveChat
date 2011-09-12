<?php

namespace ServerGrove\LiveChatBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ServerGrove\LiveChatBundle\Document;

abstract class TestCase extends WebTestCase
{

    private $cacheEngineName;
    private $shutdownFunctions;

    protected function registerShutdownFunction($callable)
    {
        $this->shutdownFunctions[] = $callable;
    }

    private function shutdown()
    {
        array_walk($this->shutdownFunctions, function($callback)
            {
                if (is_callable($callback)) {
                    call_user_func($callback);
                }
            });
    }

    protected function tearDown()
    {
        $this->shutdown();
        parent::tearDown();
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->preSetup();
        parent::setUp();
        $this->shutdownFunctions = array();
        static::$kernel = self::createKernel();
        static::$kernel->boot();
    }

    /**
     * @return void
     */
    protected function preSetup()
    {
        $this->setCacheEngineName('mongo');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return self::$kernel->getContainer();
    }

    /**
     * @param string $name
     * @return void
     */
    protected function setCacheEngineName($name)
    {
        $this->cacheEngineName = $name;
    }

    /**
     * @return string
     */
    protected function getCacheEngineName()
    {
        return $this->cacheEngineName;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager;
     */
    protected function getDocumentManager()
    {
        if (!static::$kernel) {
            throw new \Exception('You need to create a kernel instance before you can access the container');
        }

        return static::$kernel->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Session
     */
    protected function getTestSession()
    {
        $session = Document\Session::create($this->getTestVisit(), 'Test question', Document\Session::STATUS_WAITING);
        $this->saveDocument($session);
        $this->removeDocumentOnShutdown($session);

        return $session;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\CannedMessage
     */
    protected function getTestCannedMessage()
    {
        $cannedMessage = new Document\CannedMessage();
        $cannedMessage->setTitle('Canned message title');
        $cannedMessage->setContent('Canned message content');

        $this->saveDocument($cannedMessage);
        $this->removeDocumentOnShutdown($cannedMessage);

        return $cannedMessage;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Visit
     */
    protected function getTestVisit()
    {
        $visit = new Document\Visit();
        $visit->setVisitor($this->getTestVisitor());
        $visit->setRemoteAddr('8.8.8.8');

        $this->saveDocument($visit);
        $this->removeDocumentOnShutdown($visit);

        return $visit;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Visitor
     */
    protected function getTestVisitor()
    {
        $visitor = new Document\Visitor();
        $visitor->setAgent('Testing Agent 1.0.0');
        $visitor->setName('Test Name');
        $visitor->setRemoteAddr('8.8.8.8');
        $visitor->setEmail(md5(microtime(true) . rand(1, 2000)) . '@example.com');

        $this->saveDocument($visitor);
        $this->removeDocumentOnShutdown($visitor);

        return $visitor;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    protected function getTestOperator()
    {
        $operator = new Document\Operator();
        $operator->setName('Ismael Ambrosi');
        $operator->setEmail('ismael@servergrove.com');
        $operator->setPasswd('ismapass');

        $this->saveDocument($operator);
        $this->removeDocumentOnShutdown($operator);

        return $operator;
    }

    /**
     * @param $document
     * @return void
     */
    protected function saveDocument($document)
    {
        $dm = $this->getDocumentManager();
        $dm->persist($document);
        $dm->flush();
    }

    /**
     * @param $document
     * @return void
     */
    protected function removeDocumentOnShutdown($document)
    {
        $dm = $this->getDocumentManager();
        $this->registerShutdownFunction(function() use ($document, $dm)
            {
                /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm->remove($document);
                $dm->flush();
            });
    }

}
