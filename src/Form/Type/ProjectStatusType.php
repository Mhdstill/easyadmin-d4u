<?php

namespace App\Form\Type;

use App\Enum\ProjectStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => ProjectStatus::class,
            'choice_label' => fn ($choice) => match($choice) {
                ProjectStatus::PLANNED => 'Planifié',
                ProjectStatus::IN_PROGRESS => 'En cours',
                ProjectStatus::COMPLETED => 'Terminé',
                ProjectStatus::CANCELLED => 'Annulé',
                default => $choice->name,
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
} 