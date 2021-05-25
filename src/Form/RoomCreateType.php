<?php

namespace App\Form;

use App\Entity\Room;
use App\Validator\UniqueRoomName;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RoomCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Enter room Name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [new NotBlank(), new UniqueRoomName()]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Room Type',
                'required' => true,
                'choices' => [
                    'Public' => Room::STATUS_PUBLIC,
                    'Private' => Room::STATUS_PRIVATE,
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}