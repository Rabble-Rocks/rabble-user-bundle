<?php

namespace Rabble\UserBundle\Entity;

use Gedmo\Timestampable\Traits\Timestampable;

class UserActivity
{
    use Timestampable;

    protected int $id;

    protected string $type;

    protected string $translationId;

    protected ?array $translationParams;

    protected ?string $url;

    protected ?array $payload;

    protected ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getTranslationId(): string
    {
        return $this->translationId;
    }

    public function setTranslationId(string $translationId): void
    {
        $this->translationId = $translationId;
    }

    public function getTranslationParams(): ?array
    {
        return $this->translationParams;
    }

    public function setTranslationParams(?array $translationParams): void
    {
        $this->translationParams = $translationParams;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(?array $payload): void
    {
        $this->payload = $payload;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
