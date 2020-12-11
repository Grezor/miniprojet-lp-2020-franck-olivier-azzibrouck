<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class,array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Email:'
                )
            ))
            ->add('nom',TextType::class,array(
                'attr' => array(
                    'placeholder' => 'Nom:'
                ),
                'label' => false,
            ))
            ->add('prenom',TextType::class,array(
                'attr' => array(
                    'placeholder' => 'Prenom:'
                ),
                'label' => false,
            ))
            ->add('captcha',TextType::class,array(
                'attr' => array(
                ),
                'label' => false,
            ))
            ->add('formule')
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'valider vos informations',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'valider vos informations',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Vos mots de passes ne concordent pas',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => false, 'attr' => array(
                    'placeholder' => 'saisir mot de passe...',
                )],
                'second_options' => ['label' => false,'attr' => array(
                    'placeholder' => 'saisir à nouveau mot de passe...',
                ),],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}