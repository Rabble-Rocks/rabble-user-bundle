<?php

namespace Rabble\UserBundle\FieldType;

use Rabble\FieldTypeBundle\FieldType\AbstractFieldType;
use Rabble\UserBundle\Form\UserChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractFieldType
{
    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder->add($this->options['name'], UserChoiceType::class, $this->options['form_options']);
    }
}
