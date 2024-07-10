<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    public function setSender(Test1 $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSender(): ?Test1
    {
        return $this->sender;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $receiverId;

    public function setReceiverId(int $receiverId): self
    {
        $this->receiverId = $receiverId;
        return $this;
    }

    public function getReceiverId(): ?int
    {
        return $this->receiverId;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $sentAt;

    public function setSentAt(DateTime $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commission")
     * @ORM\JoinColumn(nullable=true)
     */
    private $commission; // Null si le message est global

    public function setCommission(?Commission $commission): self
    {
        $this->commission = $commission;
        return $this;
    }

    public function getCommission(): ?Commission
    {
        return $this->commission;
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $isGlobal; // True si le message est global

    public function setIsGlobal(bool $isGlobal): self
    {
        $this->isGlobal = $isGlobal;
        return $this;
    }

    public function getIsGlobal(): ?bool
    {
        return $this->isGlobal;
    }

    // Add other necessary getters and setters if needed
}