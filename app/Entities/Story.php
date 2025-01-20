<?php

declare(strict_types=1);

namespace App\Entities;

use App\Repositories\StoryRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
//#[ORM\Entity]
#[ORM\Table(name: 'stories')]
class Story
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id = null;

    #[ORM\Column(type: 'integer')]
    private int $user_id;

    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\Column(type: 'boolean')]
    private bool $active;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string')]
    private string $text;

    #[ORM\Column(type: 'integer')]
    private int $rating;

    #[ORM\Column(type: 'integer')]
    private int $reads;

    #[ORM\Column(type: 'boolean')]
    private bool $locked;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $created_at;

    #[OneToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User|null $user = null;


    public function user(): User
    {
        return $this->user;
    }

    /*#[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'], columnDefinition: "DATETIME on update CURRENT_TIMESTAMP")]
    private DateTime $updated_at;*/

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function getReads(): int
    {
        return $this->reads;
    }

    public function setReads(int $reads): void
    {
        $this->reads = $reads;
    }

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }
}
