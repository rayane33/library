<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //On utilise le builder de form pour créer les inputs
        //de notre formulaire, chaque input correspondant
        //generalement a une propireité d'entité et donc une colonne a la table

        $builder
            ->add('title')
            ->add('nbPages' ,null ,[
                "label" => "Nombre de pages"
            ])
            ->add('genre', EntityType::class, [
                "class" => Genre::class,
                "choice_label" => "name"
            ])
            ->add ("author" , EntityType::class,[
                "class" => Author::class,
                "choice_label" => function ($author){
                return $author->getlastName()." ".$author->getfirstName();
                }
            ])
            ->add('resume')
            ->add('bookCover', FileType::class, [
                'mapped' => false
            ])
            ->add("submit", SubmitType::class ,[
                "label" => "Envoyer"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
