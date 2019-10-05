<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @UniqueEntity(fields={"title"}, message="Le titre {{ value }} est déjà utilisé, veuillez en choisir un autre.")
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"ad"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un titre d'annonce.")
     * @Assert\Length(min=20, minMessage="Vous devez renseigner un titre d'annonce d'au moins {{ limit }} caractères.")
     * @Groups({"ad"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous devez renseigner un contenu d'annonce.")
     * @Assert\Length(min=50, minMessage="Vous devez renseigner un titre d'annonce d'au moins {{ limit }} caractères.")
     * @Groups({"ad"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un type d'annonce (Vente ou Recherche).")
     * @Groups({"ad"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"ad"})
     */
    private $price;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"ad"})
     */
    private $pictures;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Vous devez renseigner un annonceur.")
     * @Groups({"ad"})
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ad"})
     */
    private $slug;

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPictures(): ?array
    {
        return $this->pictures;
    }

    public function setPictures(?array $pictures): self
    {
        $this->pictures = $pictures;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
