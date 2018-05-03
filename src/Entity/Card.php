<?php
declare(strict_types=1);
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 */
class Card
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $backsideContent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string")
     */
    private $frontsideContent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastCycle;

    /**
     * @ORM\Column(type="smallint")
     */
    private $score = 0;

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

    public function getBacksideContent(): string
    {
        return (string)$this->backsideContent;
    }

    public function setBacksideContent(string $backsideContent): void
    {
        $this->backsideContent = $backsideContent;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getFrontsideContent(): string
    {
        return (string)$this->frontsideContent;
    }

    public function setFrontsideContent(string $frontsideContent): void
    {
        $this->frontsideContent = $frontsideContent;
    }

    public function getLastCycle(): ?\DateTime
    {
        return $this->lastCycle;
    }

    public function getScore(): int
    {
        return (int)$this->score;
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
        $this->score++;
        $this->lastCycle = new \DateTime('now');
    }

    public function fail(): void
    {
        $this->score = 0;
        $this->lastCycle = new \DateTime('now');
    }

    /**
     * The weight represents the relative probability of card to be picked for a cycle.
     * The factors are:
     *
     * 1. Days since last cycle (linearly)
     *    A card that has twice the days since its last cycle than another card is twice as likely to be picked.
     *    A card that was not cycled before is assumed to have 100 days since its last cycle for the purpose of this calculation.
     *    E.g. A card that has 8 days since its last cycle is 4 times as likely to be picked as a card that has 2 days since its last cycle.
     *
     * 2. Score (exponentially)
     *    A card with 1 score point higher is half as likely to be picked.
     *    E.g. A card with score 1 is 4 times as likely to be picked as a card with score 3.
     *
     * @return int
     */
    public function getWeight(): int
    {
        if ($this->getLastCycle() instanceof \DateTimeInterface) {
            $daysSinceLastCycle = (new \DateTime('now'))->diff($this->getLastCycle())->days + 1;
        } else {
            $daysSinceLastCycle = 100;
        }
        return (int)\ceil(1000 * $daysSinceLastCycle / (2 ** ($this->getScore() + 1)));
    }

    public function getPublicResource(): array
    {
        return [
            'id' => $this->getId(),
            'backsideContent' => $this->getBacksideContent(),
            'created' => $this->getCreated()->format('U'),
            'frontsideContent' => $this->getFrontsideContent(),
            'lastCycle' => $this->getLastCycle() ? $this->getLastCycle()->format('U') : null,
            'score' => $this->getScore(),
            'subjectId' => $this->getSubject()->getId(),
        ];
    }
}
