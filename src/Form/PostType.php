<?php

namespace App\Form;

use App\Entity\Post;
// use App\Entity\User;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    // Hacer que los campos title, content e image sean obligatorios
    // Añadir las clases css a los campos del formulario
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            // Crear un campo de tipo file para la imagen, "mapped" es para que no se guarde en la base de datos, si no que se guarde la imagen en la carpeta "public/images/blog"
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('Send', SubmitType::class, [
                'attr' => ['class' => 'btn btn-default pull-right']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
