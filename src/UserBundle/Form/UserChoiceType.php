<?php

namespace Rabble\UserBundle\Form;

use Rabble\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserChoiceType extends AbstractType
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addNormalizer('choices', function (Options $options, array $currentChoices) {
            if ([] !== $currentChoices) {
                return $currentChoices;
            }
            $choices = [];
            $users = $this->userRepository->findAll();
            foreach ($users as $user) {
                $choices[$user->getFirstName().' '.$user->getLastName()] = $user->getId();
            }

            return $choices;
        });
    }
}
