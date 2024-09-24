<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'El nombre no puede estar vacío.']),
                new Assert\Length(['min' => 3, 'max' => 15, 'minMessage' => 'El nombre debe tener al menos {{ limit }} caracteres.', 'maxMessage' => 'El nombre no puede tener más de {{ limit }} caracteres.'])
            ]
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'El correo electrónico no puede estar vacío.']),
                new Assert\Email(['message' => 'El correo electrónico no es válido.']),
                new Assert\Length(['max' => 180, 'maxMessage' => 'El correo electrónico no puede tener más de {{ limit }} caracteres.'])
            ]
        ])
        ->add('password', PasswordType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'La contraseña no puede estar vacía.']),
                new Assert\Length(['min' => 8, 'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres.']),
                new Assert\Regex([
                    'pattern' => '/[A-Z]/',
                    'message' => 'La contraseña debe contener al menos una letra mayúscula.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[a-z]/',
                    'message' => 'La contraseña debe contener al menos una letra minúscula.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[0-9]/',
                    'message' => 'La contraseña debe contener al menos un número.',
                ]),
                new Assert\Regex([
                    'pattern' => '/[\W_]/',
                    'message' => 'La contraseña debe contener al menos un carácter especial.',
                ]),
            ]
        ])
        ->add('role', ChoiceType::class, [
            'choices' => [
                'Usuario' => 'ROLE_USER',
                'Administrador' => 'ROLE_ADMIN',
                // Puedes añadir más roles aquí
            ],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Debes seleccionar un rol.']),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
