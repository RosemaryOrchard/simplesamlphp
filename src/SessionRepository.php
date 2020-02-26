<?php

use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
{
    public function removeExpiredSessions()
    {
        $expired = $this
            ->getEntityManager()
            ->getRepository(Session::class)
            ->findBy(['expiresAt' => new \DateTime()]);
        foreach ($expired as $record) {
            $this->getEntityManager()->remove($record);
        }
        $this->getEntityManager()->flush();
    }
}
