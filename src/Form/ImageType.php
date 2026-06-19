<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Image;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file')
            ->add('numLikes', null, ['attr' => ['class' => 'form-control']])
            ->add('numViews', null, ['attr' => ['class' => 'form-control']])
            ->add('numDownloads', null, ['attr' => ['class' => 'form-control']])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('Send', SubmitType::class, [
                'attr' => ['class' => 'pull-right btn btn-lg sr-button']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
