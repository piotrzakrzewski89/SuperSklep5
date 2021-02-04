<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Language;
use App\Entity\SellingItem;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('price', NumberType::class, [
                'translation_domain' => 'app',
            ])
            ->add('quantity', NumberType::class, [
                'translation_domain' => 'app',
            ])
            ->add('expiry_data', DateTimeType::class, [
                'translation_domain' => 'app',
                'data' => new \DateTime()
            ])
            ->add('publication', ChoiceType::class, [
                'translation_domain' => 'app',
                'choices'  => [
                    'Tak' => 1,
                    'Nie' => 0
                ],
            ])
            ->add('category', EntityType::class, [
                'translation_domain' => 'app',
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.publication = true');
                },
                'choice_label' => 'name',
            ])
            ->add('language', EntityType::class, [
                'translation_domain' => 'app',
                'class' => Language::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.publication = true');
                },
                'choice_label' => 'name',
            ])
            ->add('images', FileType::class, [
                'translation_domain' => 'app',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'app',
                'required' => false,
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
