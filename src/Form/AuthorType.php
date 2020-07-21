<?php

namespace App\Form;

use App\Entity\Author;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //On utilise le builder de form pour créer les inputs
        //de notre formulaire, chaque input correspondant
        //generalement a une propireité d'entité et donc une colonne a la table
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('birthDate',DateTimeType::class, [
                'widget' => 'single_text',
                ])
            ->add('deathDate',null,[
                "widget" => "single_text"
            ])
            ->add('biography')
            ->add('published')
            ->add ("submit" , SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
