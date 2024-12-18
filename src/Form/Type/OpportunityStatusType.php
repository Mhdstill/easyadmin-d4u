<?php

namespace App\Form\Type;

use App\Enum\OpportunityStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpportunityStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => OpportunityStatus::class,
            'choice_label' => fn ($choice) => match($choice) {
                OpportunityStatus::NEW => 'Nouveau',
                OpportunityStatus::NEGOTIATION => 'Négociation',
                OpportunityStatus::WON => 'Gagné',
                OpportunityStatus::LOST => 'Perdu',
                default => $choice->name,
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
} 