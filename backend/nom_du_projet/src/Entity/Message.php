<?php
// src/Entity/Message.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity] // Attribut pour marquer la classe comme une entité Doctrine
#[ORM\Table(name: "message")] // Attribut pour définir le nom de la table
class Message
{
    #[ORM\Id] // Attribut pour marquer le champ comme clé primaire
    #[ORM\GeneratedValue] // Attribut pour indiquer que la valeur est générée automatiquement
    #[ORM\Column(type: 'integer')] // Attribut pour définir le type de colonne
    private ?int $id = null;

    #[ORM\Column(type: 'text')] // Attribut pour définir une colonne de type texte
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)] // Attribut pour définir la relation ManyToOne avec l'entité User
    #[ORM\JoinColumn(nullable: false)] // Attribut pour définir la colonne de jointure
    private ?User $sender = null;

    #[ORM\Column(type: 'datetime')] // Attribut pour définir une colonne de type datetime
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and Setters...
    public function getId(): ?int
    {
        return $this->id;
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

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}