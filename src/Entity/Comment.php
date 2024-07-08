<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La note doit être supérieure ou égale à 0 !')]
    #[Assert\LessThanOrEqual(value: 5, message: 'La note doit être inférieure ou égale à 5 !')]
    private ?int $rating = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom complet est obligatoire !')]
    private ?string $fullname = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le contenu du commentaire est obligatoire !')]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $isValid = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): static
    {
        $this->isValid = $isValid;

        return $this;
    }
}
