<?php

namespace App\Entity;

use App\Repository\PreliminaryRegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreliminaryRegistrationRepository::class)]
#[ORM\Table(name: 'v2_preliminary_registrations')]
class PreliminaryRegistration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $passwordHash;

    // 'individual' | 'business'
    #[ORM\Column(length: 20)]
    private string $customerType;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $nip = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country = 'PL';

    #[ORM\Column]
    private bool $unregisteredBusiness = false; // Działalność nierejestrowana

    #[ORM\Column]
    private bool $b2b = false; // marker for B2B (if NIP provided or business type)

    #[ORM\Column(length: 50)]
    private string $status = 'step1_completed';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ssoProvider = null; // 'google' | 'facebook' | 'apple'

    #[ORM\Column(length: 64, unique: true)]
    private string $token; // continuation token

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->token = bin2hex(random_bytes(16));
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPasswordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $passwordHash): self { $this->passwordHash = $passwordHash; return $this; }

    public function getCustomerType(): string { return $this->customerType; }
    public function setCustomerType(string $customerType): self { $this->customerType = $customerType; return $this; }

    public function getNip(): ?string { return $this->nip; }
    public function setNip(?string $nip): self { $this->nip = $nip; return $this; }

    public function getCountry(): ?string { return $this->country; }
    public function setCountry(?string $country): self { $this->country = $country; return $this; }

    public function isUnregisteredBusiness(): bool { return $this->unregisteredBusiness; }
    public function setUnregisteredBusiness(bool $flag): self { $this->unregisteredBusiness = $flag; return $this; }

    public function isB2b(): bool { return $this->b2b; }
    public function setB2b(bool $b2b): self { $this->b2b = $b2b; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getSsoProvider(): ?string { return $this->ssoProvider; }
    public function setSsoProvider(?string $provider): self { $this->ssoProvider = $provider; return $this; }

    public function getToken(): string { return $this->token; }
    public function regenerateToken(): self { $this->token = bin2hex(random_bytes(16)); return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $dt): self { $this->createdAt = $dt; return $this; }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeInterface $dt): self { $this->updatedAt = $dt; return $this; }
}

