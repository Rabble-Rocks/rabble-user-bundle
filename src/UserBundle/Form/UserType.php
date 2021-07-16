<?php

namespace Rabble\UserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    private EntityManagerInterface $entityManager;
    private AuthorizationCheckerInterface $checker;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $checker)
    {
        $this->entityManager = $entityManager;
        $this->checker = $checker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'label' => 'form.user.label.username',
            'translation_domain' => 'RabbleUserBundle',
            'attr' => [
                'placeholder' => 'form.user.placeholder.username',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('userRoles', EntityType::class, [
            'label' => 'form.user.label.user_roles',
            'translation_domain' => 'RabbleUserBundle',
            'class' => $this->entityManager->getRepository('Role')->getClassName(),
            'choice_label' => 'name',
            'choices' => call_user_func(function () {
                $roles = $this->entityManager->getRepository('Role')->findAll();
                $canOverrule = $this->checker->isGranted('role.overrule');
                foreach ($roles as $i => $role) {
                    if (!$this->checker->isGranted($role->getRoleName()) && !$canOverrule) {
                        unset($roles[$i]);
                    }
                }

                return $roles;
            }),
            'multiple' => true,
            'expanded' => true,
        ]);
        $builder->add('password', RepeatedType::class, [
            'translation_domain' => 'RabbleUserBundle',
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'form.user.label.password',
            ],
            'second_options' => [
                'label' => 'form.user.label.password_repeat',
            ],
            'constraints' => [
                new Length([
                    'min' => 6,
                ]),
            ],
            'required' => false,
        ]);
        $builder->add('firstName', TextType::class, [
            'label' => 'form.user.label.first_name',
            'translation_domain' => 'RabbleUserBundle',
            'attr' => [
                'placeholder' => 'form.user.placeholder.first_name',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('lastName', TextType::class, [
            'label' => 'form.user.label.last_name',
            'translation_domain' => 'RabbleUserBundle',
            'attr' => [
                'placeholder' => 'form.user.placeholder.last_name',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('imageFile', VichImageType::class, [
            'label' => 'form.user.label.image_file',
            'translation_domain' => 'RabbleUserBundle',
            'image_uri' => false,
            'download_uri' => false,
            'delete_label' => 'form.user.label.delete_image',
            'required' => false,
        ]);
        $builder->add('bio', TextareaType::class, [
            'label' => 'form.user.label.bio',
            'translation_domain' => 'RabbleUserBundle',
            'attr' => [
                'rows' => 5,
                'placeholder' => 'form.user.placeholder.bio',
            ],
            'required' => false,
        ]);
    }
}
