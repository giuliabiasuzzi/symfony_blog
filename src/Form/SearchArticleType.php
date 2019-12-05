<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // formulaire de recherche en get (pas en post)
            ->setMethod('GET')
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre',
                    // les champs de recherche ne sont pas obligatoires
                    'required' => false
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'label' => 'Catégorie',
                    'class' => Category::class,
                    'choice_label' => 'name',
                    //placeholder pour avoir une categorie vide
                    'placeholder' => 'Choisissez une catégorie',
                    'required' => false
                ]
            )
            ->add(
                'start_date',
                DateType::class,
                [
                    'label' => 'Date de début',
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add(
                'end_date',
                DateType::class,
                [
                    'label' => 'Date de fin',
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
