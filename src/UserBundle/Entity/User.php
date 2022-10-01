<?php

namespace Rabble\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Timestampable\Traits\Timestampable;
use Rabble\UserBundle\UserActivity\AuditActivity\AuditSubjectInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 * @UniqueEntity("username")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable, EquatableInterface, AuditSubjectInterface
{
    use Timestampable;

    protected int $id;

    protected string $username;
    /**
     * @Assert\NotBlank()
     */
    protected ?string $password;

    protected bool $superAdmin = false;

    protected string $firstName;

    protected string $lastName;

    protected ?string $bio;

    /** @var Collection<Role> */
    protected Collection $roles;

    /** @var PersistentCollection<UserSetting> */
    protected Collection $settings;

    protected EmbeddedFile $image;

    protected ?string $avatar;

    /**
     * @Vich\UploadableField(mapping="user", fileNameProperty="image.name", size="image.size", mimeType="image.mimeType", originalName="image.originalName", dimensions="image.dimensions")
     */
    protected ?File $imageFile = null;

    public function __construct()
    {
        $this->settings = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->image = new EmbeddedFile();
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_RABBLE_ADMIN'];
        $children = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getRoleName();
            $children[] = $role->getId();
        }
        foreach ($this->roles as $role) {
            foreach ($role->getHierarchy($children) as $parentRole) {
                $roles[] = $parentRole->getRoleName();
            }
        }
        if ($this->isSuperAdmin()) {
            $roles[] = 'ROLE_RABBLE_SUPER_ADMIN';
        }

        return array_unique($roles);
    }

    /**
     * @return Collection<Role>
     */
    public function getUserRoles()
    {
        return $this->roles;
    }

    public function setUserRoles($roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return ArrayCollection<UserSetting>
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function setSettings(ArrayCollection $settings): void
    {
        $this->settings = $settings;
    }

    public function getSetting($key, ?string $default = null): ?string
    {
        if (!isset($this->settings) || !$this->settings instanceof PersistentCollection) {
            return $default;
        }
        $setting = $this->settings->matching(Criteria::create()->where(Criteria::expr()->eq('key', $key)));
        if (0 === $setting->count()) {
            return $default;
        }
        $setting = $setting->first();

        return $setting->getValue();
    }

    public function addSetting($key, string $value): void
    {
        if (!$this->settings instanceof PersistentCollection) {
            return;
        }
        $setting = $this->settings->matching(Criteria::create()->where(Criteria::expr()->eq('key', $key)))->first();
        if (!$setting instanceof UserSetting) {
            $setting = new UserSetting();
            $setting->setUser($this);
            $this->settings->add($setting);
        }
        $setting->setKey($key);
        $setting->setValue($value);
    }

    /**
     * @return Role[]
     */
    public function getRoleHierarchy(): array
    {
        $roles = [];
        $children = [];
        foreach ($this->roles as $role) {
            $roles[] = $role;
            $children[] = $role->getId();
        }
        foreach ($roles as $role) {
            foreach ($role->getHierarchy($children) as $parentRole) {
                $roles[] = $parentRole;
                $children[] = $parentRole->getId();
            }
        }

        return array_unique($roles, SORT_REGULAR);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        if (null !== $password) {
            $this->password = $password;
        }
    }

    public function isSuperAdmin(): bool
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(bool $superAdmin): void
    {
        $this->superAdmin = $superAdmin;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(EmbeddedFile $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?EmbeddedFile
    {
        return $this->image;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return null|string
     */
    public function getSalt()
    {
        return 'RabbleSalt';
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->username,
            $this->password
        ] = unserialize($serialized, [static::class]);
    }

    /**
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        return $this->username === $user->getUserIdentifier();
    }

    public function getAuditSubject(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function getAuditSubjectType(): string
    {
        return 'user';
    }
}
