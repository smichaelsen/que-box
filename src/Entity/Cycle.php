<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CycleRepository")
 */
class Cycle
{

    public const RESULT_SUCCESS = 1;

    public const RESULT_FAILURE = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     * @ORM\JoinColumn(nullable=true)
     */
    private $card;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $completed;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $scoreChange;

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function setCard(Card $card): void
    {
        $this->card = $card;
    }

    public function getResult(): ?int
    {
        if ($this->result === null) {
            return null;
        }
        return (int) $this->result;
    }

    public function succeed(): void
    {
        $this->result = self::RESULT_SUCCESS;
        $this->scoreChange = 1;
        $this->getCard()->succeed();
        $this->completed = new \DateTime('now');
    }

    public function fail(): void
    {
        $this->result = self::RESULT_FAILURE;
        $this->scoreChange = - $this->getCard()->getScore();
        $this->getCard()->fail();
        $this->completed = new \DateTime('now');
    }
}