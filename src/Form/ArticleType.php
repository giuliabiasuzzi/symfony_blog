<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre'
                ])
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu'
                ])
            ->add(
                'category',
                // select sur une entité Doctrine
                //type de form qui va faire une liste deroulante
                // avec les categories dispos
                EntityType::class,
                [
                    'label' => 'Catégorie',
                    // nom de l'entité qui contient les categories
                    'class' => Category::class,
                    // mon de l'attribu de Category qui s'affiche ds le select
                    'choice_label' => 'name',
                    // 1ère opt vide qui oblige à choisir
                    'placeholder' => 'Choisissez une catégorie'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
