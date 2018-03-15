<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubjectRepository")
 */
class Subject
{
    public const TYPE_LANGUAGE = 1;

    public const TYPE_QUESTIONS = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type = self::TYPE_LANGUAGE;

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getTitle(): string
    {
        return (string)$this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getType(): int
    {
        return (int)$this->type;
    }

    public function setType(int $type): void
    {
        \assert(\in_array($type, [self::TYPE_LANGUAGE, self::TYPE_QUESTIONS], true));
        $this->type = $type;
    }
}
