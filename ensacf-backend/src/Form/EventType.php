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
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text'
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'événement',
                'choices' => [
                    'Exposition' => 'exposition',
                    'Conférence' => 'conference',
                    'Table Ronde' => 'table_ronde',
                    'Séminaire' => 'seminaire',
                    'Résidence' => 'residence',
                    'Vie Étudiante (Bistrot d\'archi... Association)' => 'vie_etudiante',
                    'Événements Externes' => 'evenements_externes',
                    'Autres' => 'autres',
                    'Vie de l\'École (Instance, Réunion Interne)' => 'vie_ecole'
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'Lieu'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('additionalInfo', TextareaType::class, [
                'label' => 'Autres informations (besoin salle, inauguration, besoin (pô cocktail))',
                'required' => false
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de l\'événement',
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}