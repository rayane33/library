<?php


//je crée un namespace unique pour ma class qui utilise le chemin du fichier
namespace App\Controller;


use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    /**
     * @Route("/", name="home")
     */
    public function home (AuthorRepository $authorRepository,
                          BookRepository $bookRepository,
                          GenreRepository $genreRepository
    )
    {
        $authors = $authorRepository->findBy([],['id'=> 'DESC'],3);
        $books = $bookRepository -> findBy([],['id'=> 'DESC'],3);


        return $this->render("home.html.twig",[
            "authors" => $authors,
            "books" => $books,
            ]);
    }

}