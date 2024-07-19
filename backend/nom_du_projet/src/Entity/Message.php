<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: GroupConversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupConversation $group = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commission $commission = null;

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

    public function getGroup(): ?GroupConversation
    {
        return $this->group;
    }

    public function setGroup(?GroupConversation $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getCommission(): ?Commission
    {
        return $this->commission;
    }

    public function setCommission(?Commission $commission): self
    {
        $this->commission = $commission;
        return $this;
    }
}