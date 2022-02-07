<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email', Type\EmailType::class)
            ->add('phone', Type\TelType::class)
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
            ])
            ->add('terms', Type\CheckboxType::class, [
                'label' => 'Click here to indicate that you have read and agree to the terms presented in the Terms and Conditions agreement',
                'help' => 'Your email and information are used to allow you to sign in securely and access your data. SensioTV records certain usage data for security, support and reporting purposes.',
            ])
            ->add('save', Type\SubmitType::class, [
                'label' => 'Create your SensioTV account',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
