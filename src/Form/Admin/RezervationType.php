<?php

namespace App\Form\Admin;

use App\Entity\Admin\Rezervation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RezervationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userid')
            ->add('hotelid')
            ->add('roomid')
            ->add('name')
            ->add('surname')
            ->add('email')
            ->add('phone')
            ->add('checkin')
            ->add('checkout')
            ->add('days')
            ->add('total')
            ->add('message')
            ->add('note')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'New' => 'New',
                    'Accepted' => 'Accepted',
                    'Cancelled' => 'Cancelled',
                    'Completed' => 'Completed'
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rezervation::class,
        ]);
    }
}
