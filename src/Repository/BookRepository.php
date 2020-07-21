<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }


    public function findByWordsInResume($word){

        // j'utilise la méthode createQueryBuilder
        // pour récupérer une instance de la classe QueryBuilder
        // qui nous permet de créer la requête "SQL"
        // Je passe en parametre un alias pour représenter
        // ma table book (je peux mettre ce que je veux en alias
        // pour représenter ma table book)
        $queryBuilder = $this-> createQueryBuilder("book");

        // j'utilise le queryBuilder pour selectionner tout ce
        // qu'il y a dans ma table book (en utilisant l'alias)
        $query = $queryBuilder->select('book')

            // je créé une clause where (comme en SQL)
            // pour chercher dans la colonne 'resume'
            // un mot. Je ne passe pas directement le mot
            // provenant de l'utilisateur mais j'utilise un
            // 'placeholder' car il faut sécuriser son contenu
            // avant de faire la requête, pour éviter notammement
            // les injections SQL.
            // "On ne fait jamais confiance à l'utilisateur"
            ->where("book.resume LIKE :word")

            // j'utilise la méthode setParameter pour remplacer
            // dans la requête SQL du "where" le 'placeholder' par
            // la vraie valeur. Le setParameter permet de sécuriser la
            // donnée utilisateur (supprime les balises SQL etc)
            -> setParameter("word","%".$word."%")

            // je récupère la requête "SQL" finale
            ->getQuery();

        // j'execute ma requête et je stocke les résultats dans une variable
        $books  = $query->getResult();


        // je retourne les résultats
        return $books;

    }
}
