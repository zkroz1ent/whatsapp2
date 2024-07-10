<?php
   // src/Entity/User.php

   namespace App\Entity;

   use App\Repository\UserRepository;
   use Doctrine\ORM\Mapping as ORM;
   use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
   use Symfony\Component\Security\Core\User\UserInterface;

   #[ORM\Entity(repositoryClass: UserRepository::class)]
   #[ORM\Table(name: "user")]
   class User implements UserInterface, PasswordAuthenticatedUserInterface
   {
       #[ORM\Id]
       #[ORM\GeneratedValue]
       #[ORM\Column(type: 'integer')]
       private ?int $id = null;

       #[ORM\Column(type: 'string', length: 180, unique: true)]
       private ?string $username = null;

       #[ORM\Column(type: 'json')]
       private array $roles = [];

       #[ORM\Column(type: 'string')]
       private ?string $password = null;

       #[ORM\Column(type: 'string', length: 180, unique: true)]
       private ?string $email = null;

       // Getters and Setters...
       public function getId(): ?int
       {
           return $this->id;
       }

       public function getUsername(): ?string
       {
           return $this->username;
       }

       public function setUsername(string $username): self
       {
           $this->username = $username;
           return $this;
       }

       public function getPassword(): ?string
       {
           return $this->password;
       }

       public function setPassword(string $password): self
       {
           $this->password = $password;
           return $this;
       }

       public function getRoles(): array
       {
           return $this->roles;
       }

       public function setRoles(array $roles): self
       {
           $this->roles = $roles;
           return $this;
       }

       public function getEmail(): ?string
       {
           return $this->email;
       }

       public function setEmail(string $email): self
       {
           $this->email = $email;
           return $this;
       }

       public function getUserIdentifier(): string
       {
           return $this->username;
       }

       public function eraseCredentials()
       {
           // Implémentez si nécessaire
       }
   }