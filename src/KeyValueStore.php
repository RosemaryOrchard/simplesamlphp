<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SessionRepository")
 */
class KeyValueStore
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="_key", type="string")
     * @var string
     */
    protected $key;

    /**
     * @ORM\Column(name="_value", type="string")
     * @var string
     */
    protected $value;

    /**
     * @ORM\Column(name="_expire", type="datetime", nullable=true)
     * @var DateTime
     */
    protected $expiresAt;
}
