<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Language;
use App\Entity\SellingItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class SellingItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'translation_domain' => 'app',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'app',
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'translation_domain' => 'app',
            ])
            ->add('category', EntityType::class, [
                'translation_domain' => 'app',
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('language', EntityType::class, [
                'translation_domain' => 'app',
                'class' => Language::class,
                'choice_label' => 'name',
            ])
            ->add('images', FileType::class, [
                'translation_domain' => 'app',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'translation_domain' => 'app',
                'label' => 'form.save_button_label'
            ])
            ->add('save_add_next', SubmitType::class, [
                'translation_domain' => 'app',
                'label' => 'form.save_add_next_button_label'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SellingItem::class,
        ]);
    }
}
