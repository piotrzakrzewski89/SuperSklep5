<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Language;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['translation_domain' => 'app'])
            ->add('code', TextType::class, ['translation_domain' => 'app'])
            ->add('language', EntityType::class, [
                'translation_domain' => 'app',
                'class' => Language::class,
                'choice_label' => 'name',
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
            'data_class' => Category::class,
        ]);
    }
}
