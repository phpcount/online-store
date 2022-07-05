<?php

namespace App\Form;

use App\Entity\StaticStorage\UserStaticStorage;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['data'];
        $isNewUser = is_null($user->getId());
        
        $builder
            ->add('plainPassword', TextType::class, [
                'label' => 'New password',
                'required' => $isNewUser,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => "Your password must be at least {{ limit }} characters long",
                    ]),
                    // new Regex('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W])/', 'Current password is not valid')
                    // new UserPassword() // it's old password
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'required' => false,
                'choices' => array_flip(UserStaticStorage::getUserRolesChoices()),
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Is verified',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ]
            ])
            ->add('fullName', TextType::class, [
                'label' => 'Full name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(null, 'Should be filled')
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Address',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Zip code',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('isDeleted', CheckboxType::class, [
                'label' => 'Is deleted',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ]
            ])
        ;

        if ($isNewUser) {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new Email([
                            'message' => 'Email is not valid',
                            'mode' => 'html5'
                        ])
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
