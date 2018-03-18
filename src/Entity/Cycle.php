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
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     * @ORM\JoinColumn(nullable=true)
     */
    private $card;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completed;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reversed;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $scoreChange;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subject")
     * @ORM\JoinColumn(nullable=true)
     */
    private $subject;

    public function __construct()
    {
        $this->created = new \DateTime('now');
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function setCard(Card $card): void
    {
        $this->card = $card;
    }

    public function getCompleted(): ?\DateTime
    {
        return $this->completed;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getResult(): ?int
    {
        if ($this->result === null) {
            return null;
        }
        return (int)$this->result;
    }

    public function isReversed(): bool
    {
        return (bool)$this->reversed;
    }

    public function setReversed(bool $reversed): void
    {
        $this->reversed = $reversed;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): void
    {
        $this->subject = $subject;
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
        $this->scoreChange = -$this->getCard()->getScore();
        $this->getCard()->fail();
        $this->completed = new \DateTime('now');
    }

    public function getQuestion(): string
    {
        if ($this->isReversed()) {
            return $this->getCard()->getBacksideContent();
        }
        return $this->getCard()->getFrontsideContent();
    }

    public function getAnswer(): string
    {
        if ($this->isReversed()) {
            return $this->getCard()->getFrontsideContent();
        }
        return $this->getCard()->getBacksideContent();
    }
}
