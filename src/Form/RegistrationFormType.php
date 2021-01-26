<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'register_form.email_label',
                'translation_domain' => 'app'
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'register_form.agree_terms_label',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'register_form.agree_terms_message',
                    ]),
                ],
                'translation_domain' => 'app'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Pola hasła powinny się zgadzać.',
                'mapped' => false,
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'register_form.password_first'],
                'second_options' => ['label' => 'register_form.password_second'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'register_form.password.message',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'register_form.password_message_min',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '"^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$"',
                        'message' => 'register_form.password_message_regex',
                    ]),
                ],
                'translation_domain' => 'app'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
