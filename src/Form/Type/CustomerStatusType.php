<?php

namespace App\Form\Type;

use App\Enum\CustomerStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => CustomerStatus::class,
            'choice_label' => fn ($choice) => match($choice) {
                CustomerStatus::PROSPECT => 'Prospect',
                CustomerStatus::ACTIVE => 'Client Actif',
                CustomerStatus::INACTIVE => 'Client Inactif',
                default => $choice->name,
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
} 