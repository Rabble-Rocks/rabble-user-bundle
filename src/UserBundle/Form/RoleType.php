<?php

namespace Rabble\UserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Rabble\UserBundle\Entity\Role;
use Rabble\UserBundle\Security\Task\TaskBundle;
use Rabble\UserBundle\Security\Task\TaskCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RoleType extends AbstractType
{
    /** @var TaskBundle[]|TaskCollection */
    private $taskCollection;

    private AuthorizationCheckerInterface $checker;

    private EntityManagerInterface $entityManager;

    /**
     * @param TaskBundle[]|TaskCollection $taskCollection
     */
    public function __construct(
        TaskCollection $taskCollection,
        AuthorizationCheckerInterface $checker,
        EntityManagerInterface $entityManager
    ) {
        $this->taskCollection = $taskCollection;
        $this->checker = $checker;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'form.role.label.name',
            'translation_domain' => 'RabbleUserBundle',
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 3,
                ]),
            ],
        ]);
        $canOverrule = $this->checker->isGranted('role.overrule');
        $builder->add('parent', EntityType::class, [
            'label' => 'form.role.label.parent',
            'class' => get_class($builder->getData()),
            'choice_label' => 'name',
            'choices' => call_user_func(function () use ($canOverrule, $builder) {
                /** @var Role[] $roles */
                $roles = $this->entityManager->getRepository('Role')->findAll();
                foreach ($roles as $i => $role) {
                    if ($role === $builder->getData() || (!$canOverrule && !$this->checker->isGranted($role->getRoleName()))) {
                        unset($roles[$i]);
                    }
                }

                return $roles;
            }),
            'choice_filter' => function ($choice) use ($builder) {
                return $builder->getData() !== $choice;
            },
            'translation_domain' => 'RabbleUserBundle',
            'required' => false,
        ]);
        $choices = [];
        foreach ($this->taskCollection as $taskBundle) {
            foreach ($taskBundle->getTasks() as $task) {
                $choiceName = $taskBundle->getName().'.'.$task;
                if ($canOverrule || $this->checker->isGranted($choiceName)) {
                    $choices[$choiceName] = $choiceName;
                }
            }
        }
        $builder->add('tasks', ChoiceType::class, [
            'label' => false,
            'translation_domain' => 'RabbleUserBundle',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'choices' => $choices,
            'choice_label' => function ($choice) {
                return "task.{$choice}";
            },
            'choice_translation_domain' => 'security',
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($view['tasks'] as $task) {
            $task->vars['translation_domain'] = $view['tasks']->vars['choice_translation_domain'];
        }
    }
}
