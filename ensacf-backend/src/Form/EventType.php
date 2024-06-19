<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'événement',
                'choices' => [
                    'Exposition' => 'exposition',
                    'Table Ronde' => 'table_ronde',
                    'Résidence' => 'residence',
                    'Instance' => 'instance',
                    'Autres' => 'autres'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('location', TextType::class, [
                'label' => 'Lieu'
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text'  // Utilise un seul champ de type texte pour la date
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text'  // Utilise un seul champ de type texte pour la date
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de l\'événement',
                'mapped' => false,  // Ne pas mapper automatiquement à la base de données
                'required' => false  // Rendre le champ optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}