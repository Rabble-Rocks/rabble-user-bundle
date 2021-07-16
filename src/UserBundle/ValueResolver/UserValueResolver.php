<?php

namespace Rabble\UserBundle\ValueResolver;

use Rabble\FieldTypeBundle\FieldType\FieldTypeInterface;
use Rabble\FieldTypeBundle\ValueResolver\ValueResolverInterface;
use Rabble\UserBundle\FieldType\UserType;
use Rabble\UserBundle\Repository\UserRepository;
use Webmozart\Assert\Assert;

class UserValueResolver implements ValueResolverInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed                       $value
     * @param FieldTypeInterface|UserType $fieldType
     */
    public function resolve($value, FieldTypeInterface $fieldType, ?string $target = null): ?array
    {
        if (null === $value) {
            return null;
        }
        Assert::numeric($value);
        $user = $this->userRepository->find($value);

        if (null === $user) {
            return null;
        }

        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'avatar' => $user->getAvatar(),
            'bio' => $user->getBio(),
        ];
    }

    public function supports(FieldTypeInterface $fieldType): bool
    {
        return $fieldType instanceof UserType;
    }
}
