<?php
// src/Entity/GroupConversation.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "group_conversation")]
class GroupConversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: "group_conversation_users",
        joinColumns: [#[ORM\JoinColumn(name: "group_conversation_id", referencedColumnName: "id")]],
        inverseJoinColumns: [#[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]]
    )]
    private $users;

    // Construire la Many-to-Many relation pour les messages
    #[ORM\OneToMany(mappedBy: 'groupConversation', targetEntity: Message::class)]
    private $messages;

    // Getters and Setters...
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);
        return $this;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setGroupConversation($this);
        }
        return $this;
    }
}