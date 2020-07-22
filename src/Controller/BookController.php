<?php
namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController{

    /**
     * @Route("/books", name="books_list")
     */
    public function BookList (BookRepository $bookRepository){
        $books = $bookRepository->findAll();
        return $this -> render ("books.html.twig",[
            "books"=> $books
        ]);

    }
    /**
     * @Route ("/book/{id}", name="book_show")
     */
    public function BookShow (BookRepository $bookRepository, $id){

        $book= $bookRepository->find($id);

        return $this -> render ("book.html.twig",[

            "book"=> $book
        ]);
    }

    public function listGenres (GenreRepository $genreRepository){
        $genre = $genreRepository -> findOneBy(['name'=>$name]);

        return $this -> render ("list_genre.html.twig",[
            "genres" => $genre,
        ]);
    }


    /**
     * @Route("/books/genre/{name}", name="book_genre")
     */
    public function BookByGenre (BookRepository $bookRepository,
                                 GenreRepository $genreRepository,
                                 $name
    ){
        // J'utilise le bookRepository et sa méthode findBy pour trouver un ou plusieurs livres en BDD
        // en fonction de la valeur d'une colonne ici genre
        $genre = $genreRepository -> findOneBy(['name'=>$name]);

        $books = $bookRepository ->findBy([
            "genre" => $genre
        ]);
        return $this -> render ("books_genre.html.twig",[
            "books" => $books,
            "genre" => $genre,
        ]);
    }

    /**
     * @Route("/books/search" , name="book_search_resume")
     */
    public function BookSearchByresume(
        BookRepository $bookRepository,
        Request $request
    )
    {
        // J'utilise la classe Request pour récupérer la valeur
        // du parametre d'url "search" (envoyé par le formulaire)
        $word = $request->query->get("search");

        // j'initilise une variable $books avec un tableau vide
        // pour ne pas avoir d'erreur si je n'ai pas de parametre d'url de recherche
        // et que donc ma méthode de répository n'est pas appelée
        $books = [];

        //  si j'ai des parametres d'url de recherche (donc que mon utilisateur
        // a fait une recherche
        if (!empty($word)) {

            // s'il a fait une recherche, je créé une requête SELECT
            // pour trouver les livres que l'utilisateur a recherché
            $books = $bookRepository->findByWordsInResume($word);
        }

        // j'appelle mon fichier twig avec les books trouvés en BDD
        return $this->render('search.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/books/insert",name="books_insert")
     */
    public function InsertBook(EntityManagerInterface $entityManager){

        // les entités font le lien avec les tables
        // donc pour créer un enregistrement dans ma table book
        // je créé une nouvelle instance de l'entité Book
        $book = new Book();

        // je lui donne les valeurs des colonnes avec les setters
        $book -> setTitle ("Clair de femme");
        $book -> setGenre ("Fiction");
        $book-> setNbPages(300);
        $book-> setResume ("Michel vient de perdre Yannick. Sa femme est morte d’un cancer. Lui est commandant de bord. Sortant d’un taxi il bouscule une belle femme mûre dont la vie pourrait ressembler à la sienne. Lydia Kowalski a perdu sa fille dans un accident de voiture que conduisait son mari, qui n’est plus qu’un survivant relevant de la psychiatrie. Roman d’une seule nuit où Michel et Lydia vont se connaître puis se séparer avant un nouveau rendez-vous probable…");

        // j'utilise l'entityManager pour que Doctrine
        // m'enregistre le livre créé avec la méthode persist()
        // puis je "valide" l'enregistrement avec la méthode flush()
        $entityManager ->persist($book);
        $entityManager->flush();
        return $this->render('book_insert.html.twig');
    }


    //Je déclare ici une nouvelle route permettant de modifié les données de la bdd grâce à l'EntityManager et
    //le Bookrepository ensuite je fais comme précédémentavec l'insert à l'exception qu'au lieu de crée un nouveau livre
    //on choisi lequel on va modifier grâce à l'id puis on choisit la colonne à changer.
    /**
     * @Route("/books/update" , name="book_change")
     */
    public function BookUpdate(EntityManagerInterface $entityManager, BookRepository $bookRepository){


        $book = $bookRepository ->find(4);
        $book-> setNbPages(400);

        $entityManager -> persist($book);
        $entityManager -> flush();
        return $this ->render("book_update.html.twig");

    }

    /**
     * @Route("/books/delete", name="book_delete")
     */
    public function DeleteBook(BookRepository $bookRepository, EntityManagerInterface $entityManager){
        $book= $bookRepository ->find(7);
        $entityManager -> remove($book);
        $entityManager -> flush();

        return $this ->render("book_delete.html.twig");

    }


}
