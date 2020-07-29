<?php


namespace App\Type;


use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,
                [
                    'label' => 'Başlık',
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add('description', TextareaType::class,
                [
                    'label' => 'Detay',
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add('price', TextType::class,
                [
                    'label' => 'Fiyat',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('quantity', TextType::class,
                [
                    'label' => 'Adet',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('tags', EntityType::class,
                [
                    'class'     => 'App\Entity\Tag',
                    'label' => 'Etiketler',
                    'choice_label' => 'title',
                    'expanded'=>'true',
                    'multiple'=>'true',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add('categories', EntityType::class,
                [
                    'class'     => 'App\Entity\Category',
                    'label' => 'Categori',
                    'choice_label' => 'title',
                    'expanded'=>'false',
                    'multiple'=>'false',
                    'placeholder' => 'Kategori Seçiniz',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}