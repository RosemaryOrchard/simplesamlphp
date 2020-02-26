<?php

/**
 * Session storage in the data store.
 *
 * @package SimpleSAMLphp
 */

declare(strict_types=1);

namespace SimpleSAML;

use Doctrine\ORM\EntityManagerInterface;
use SimpleSAML\Session as SimpleSAMLsession;
use Session;
use Webmozart\Assert\Assert;

class SessionHandlerStore extends SessionHandlerCookie
{
    /**
     * The data store we save the session to.
     *
     * @var \SimpleSAML\Store
     */
    private $store;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * Initialize the session.
     *
     * @param \SimpleSAML\Store $store The store to use.
     */
    protected function __construct(Store $store, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->store = $store;
        $this->entityManager = $entityManager;
    }


    /**
     * Load a session from the data store.
     *
     * @param string|null $sessionId The ID of the session we should load, or null to use the default.
     *
     * @return \SimpleSAML\Session|null The session object, or null if it doesn't exist.
     */
    public function loadSession(?string $sessionId): ?SimpleSAMLsession
    {
        if ($sessionId === null) {
            $sessionId = $this->getCookieSessionId();
            if ($sessionId === null) {
                // no session cookie, nothing to load
                return null;
            }
        }
        $session = $this->entityManager->getRepository(Session::class)->findBy(['sessionId' => $sessionId]);
        if ($session !== null) {
            $session = unserialize($session);
            Assert::isInstanceOf($session, Session::class);
            return $session;
        }

        return null;
    }


    /**
     * Save a session to the data store.
     *
     * @param \SimpleSAML\Session $simpleSAMLsession
     * @return void
     * @throws \Exception
     */
    public function saveSession(SimpleSAMLsession $simpleSAMLsession): void
    {
        if ($simpleSAMLsession->isTransient()) {
            // transient session, nothing to save
            return;
        }


        $config = Configuration::getInstance();
        $sessionDuration = $config->getInteger('session.duration', 8 * 60 * 60);

        $dbSession = new Session();
        $dbSession->setSessionId($simpleSAMLsession->getSessionId());
        $dbSession->setSession($simpleSAMLsession->serialize());
        $dbSession->setExpiresAt(new \DateTime() + $sessionDuration);

        $this->entityManager->persist($dbSession);
        $this->entityManager->flush();
    }
}
