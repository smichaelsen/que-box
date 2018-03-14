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

    public function __construct()
    {
        $this->created = new \DateTime('now');
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getBacksideContent(): string
    {
        return (string) $this->backsideContent;
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
        return (string) $this->frontsideContent;
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
        return (int) $this->score;
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

    public function getWeight(): int
    {
        if ($this->getLastCycle() instanceof \DateTimeInterface) {
            $daysSinceLastCycle = (new \DateTime('now'))->diff($this->getLastCycle())->days + 1;
        } else {
            $daysSinceLastCycle = 100;
        }
        return (int) round(1000 * $daysSinceLastCycle / (($this->getScore()+1) ** 2));
    }
}
