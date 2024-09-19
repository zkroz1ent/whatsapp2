<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\GroupConversation;
use App\Entity\Commission;
use App\Entity\Conversation;

#[ORM\Entity]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagesSent')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagesReceived')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $receiver = null;

    #[ORM\ManyToOne(targetEntity: GroupConversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?GroupConversation $group = null;

    #[ORM\ManyToOne(targetEntity: Commission::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commission $commission = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Conversation $conversation = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isGlobal = false;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $type = 'default_type'; // Fournir une valeur par défaut

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

    public function setSender(User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;
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

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function isGlobal(): bool
    {
        return $this->isGlobal;
    }

    public function setIsGlobal(bool $isGlobal): self
    {
        $this->isGlobal = $isGlobal;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}