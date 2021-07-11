<?php

namespace Rabble\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("name")
 * @Gedmo\Tree
 */
class Role
{
    use Timestampable;

    protected int $id;

    protected string $name;

    protected array $tasks = [];

    /**
     * @Gedmo\TreeParent
     */
    protected ?Role $parent;

    /** @var PersistentCollection|Role[] */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRoleName(): string
    {
        return 'ROLE_'.strtoupper(preg_replace('/[^a-z]+/i', '_', $this->name));
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return ?Role
     */
    public function getParent(): ?Role
    {
        return $this->parent;
    }

    /**
     * @param ?Role $parent
     */
    public function setParent(?Role $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return PersistentCollection|Role[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Role[]
     */
    public function getHierarchy(array $children = []): array
    {
        $parents = [];
        $children[] = $this->id;
        if ($this->parent instanceof Role && !in_array($this->parent->getId(), $children, false)) {
            $parents[] = $this->parent;
            foreach ($this->parent->getHierarchy($children) as $parent) {
                if (!in_array($parent->getId(), $children, false)) {
                    $parents[] = $parent;
                }
            }
        }

        return $parents;
    }
}
