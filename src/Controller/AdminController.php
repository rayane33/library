<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController{

    /**
     * @Route("/admin" , name="admin_home")
     */
    public function adminHome(AuthorRepository $authorRepository, BookRepository $bookRepository){
        $authors = $authorRepository->findBy([], ['id' => 'DESC'], 3);
        $books = $bookRepository->findBy([], ['id' => 'DESC'], 3);

        return $this->render("Admin/admin_home.html.twig", [
            "authors" => $authors,
            "books" => $books
        ]);
    }

    /**
     * @Route("/admin/books", name="admin_books")
     */
    public function AdminBook( BookRepository $bookRepository){
       $books = $bookRepository ->findAll();
       return $this ->render("Admin/admin_books.html.twig",
           [
                "books" => $books
           ]);
    }

    /**
     * @Route("/admin/authors", name="admin_authors")
     */
    public function AdminAuthor( AuthorRepository $authorRepository){
        $authors = $authorRepository ->findAll();
        return $this ->render("Admin/admin_authors.html.twig",
            [
                "authors" => $authors
            ]);
    }

    /**
     * @Route("/admin/authors/delete/{id}" , name="admin_author_delete")
     */
    public function AdminAuthorDelete(
        AuthorRepository $authorRepository,
        EntityManagerInterface $entityManager,
        $id
        ){
        $author = $authorRepository->find($id);

        $entityManager -> remove($author);
        $entityManager -> flush();
        return $this ->redirectToRoute("admin_authors");
    }

    /**
     * @Route("/admin/books/delete/{id}" , name="admin_book_delete")
     */
    public function AdminBookDelete(
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        $id
    ){
        $book = $bookRepository->find($id);

        $entityManager -> remove($book);
        $entityManager -> flush();
        return $this -> redirectToRoute("admin_books");
    }

    /**
     *@Route("/admin/books/insert" , name="admin_books_insert")
     */
    public function AdminInsertbook(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger)
    {
        // je créé une nouvelle instance de l'entité Book
        $book = new Book();

        // je récupère le gabarit de formulaire de
        // l'entité Book, créé avec la commande make:form
        // et je le stocke dans une variable $bookForm
        $bookform = $this->createForm(BookType::class, $book);

        // on prend les données de la requête (classe Request)
        //et on les "envoie" à notre formulaire
        $bookform ->handleRequest($request);

        // si le formulaire a été envoyé et que les données sont valides
        // par rapport à celles attendues alors je persiste le livre
        if($bookform -> isSubmitted() && $bookform -> isValid()){

            // vu que le champs bookCover de mon formulaire est en mapped false
            // je gère moi même l'enregistrment de la valeur de cet input
            // https://symfony.com/doc/current/controller/upload_file.html

            // je récupère l'image uploadée
            $bookCoverFile = $bookform->get('bookCover')->getData();
            // s'il y a bien une image uploadée dans le formulaire
            if ($bookCoverFile) {

                // je récupère le nom de l'image
                $originalCoverName = pathinfo($bookCoverFile->getClientOriginalName(), PATHINFO_FILENAME);

                // et grâce à son nom original, je gènère un nouveau qui sera unique
                // pour éviter d'avoir des doublons de noms d'image en BDD
                $safeCoverName = $slugger->slug($originalCoverName);
                $uniqueCoverName = $safeCoverName . '-' . uniqid() . '.' . $bookCoverFile->guessExtension();


                // j'utilise un bloc de try and catch
                // qui agit comme une conditions, mais si le bloc try échoue, ça
                // soulève une erreur, qu'on peut gérer avec le catch
                try {

                    // je prends l'image uploadée
                    // et je la déplace dans un dossier (dans public) + je la renomme avec
                    // le nom unique générée
                    // j'utilise un parametre (défini dans services.yaml) pour savoir
                    // dans quel dossier je la déplace
                    // un parametre = une sorte de variable globale
                    $bookCoverFile->move(
                        $this->getParameter('book_cover_directory'),
                        $uniqueCoverName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }


                // je sauvegarde dans la colonne bookCover le nom de mon image
                $book->setBookCover($uniqueCoverName);
            }

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Votre livre a été créé !');

            return $this -> redirectToRoute("admin_books");
        }

        // je retourne mon fichier twig, en lui envoyant
        // la vue du formulaire, générée avec la méthode createView()
        return $this -> render("Admin/admin_book_insert.html.twig",[
            "bookForm" => $bookform ->createView()
        ]);
    }

    /**
     *@Route("/admin/authors/insert" , name="admin_authors_insert")
     */
    public function AdminInsertauthor(
        EntityManagerInterface $entityManager,
        Request $request
    ){

        // je créé une nouvelle instance de l'entité Author
        $author = new Author();

        // je récupère le gabarit de formulaire de
        // l'entité Book, créé avec la commande make:form
        // et je le stocke dans une variable $authorForm
        $authorform = $this->createForm(AuthorType::class, $author);

        // on prend les données de la requête (classe Request)
        //et on les "envoie" à notre formulaire
        $authorform ->handleRequest($request);

        // si le formulaire a été envoyé et que les données sont valides
        // par rapport à celles attendues alors je persiste le livre
        if($authorform -> isSubmitted() && $authorform -> isValid()){
            $entityManager->persist($author);
            $entityManager->flush();
            return $this -> redirectToRoute("admin_authors");
        }

        // je retourne mon fichier twig, en lui envoyant
        // la vue du formulaire, générée avec la méthode createView()
        return $this -> render("Admin/admin_author_insert.html.twig",[
            "authorForm" => $authorform ->createView()
        ]);
    }

    /**
     * @Route("/admin/books/update/{id}" , name="admin_books_update")
     */
    public function AdminUpdateBook (
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        $id
    ){
        $book = $bookRepository ->find($id);

        $bookForm = $this ->createForm(BookType::class, $book);

        $bookForm ->handleRequest($request);

        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_books');
        }
        return $this->render('admin/admin_book_update.html.twig', [
            'bookForm' => $bookForm->createView()
        ]);
    }

    /**
     * @Route ("/admin/authors/update/{id}" , name="admin_authors_update")
     */
    public function AdminAuthorupdate(
        EntityManagerInterface $entityManager,
        AuthorRepository $authorRepository,
        Request $request,
        $id
    ){
        $author = $authorRepository -> find($id);
        $authorForm = $this -> createForm(AuthorType::class, $author);
        $authorForm -> handleRequest($request);

        if ($authorForm->isSubmitted() && $authorForm->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('admin_authors');
        }

        return $this->render('admin/admin_author_update.html.twig', [
            'authorForm' => $authorForm->createView()
        ]);
    }

    /**
     * @Route ("/admin/book/insertwithgenre" , name = "admin_book_insert_genre")
     */
    public function InsertBookWithGenre(
        GenreRepository $genreRepository,
        EntityManagerInterface $entityManager
    )
    {
        $book = new Book();
        $genre = $genreRepository -> find(2);

        $book -> setTitle("sqdd");
        $book -> setNbPages(56);
        $book -> setResume("sfsfd");
        $book -> setGenre($genre);

        $entityManager -> persist($book);
        $entityManager -> flush();

        return new Response("okok");
    }


}