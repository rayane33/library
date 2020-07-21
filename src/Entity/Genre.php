<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="genre")
     *
     * On cree la relation inverse du ManyToOne (detenue pour l'entité Book), donc un OneToMany
     * un genre peut donc avoir plusieur book
     */
    private $books;

    // Un Genre  peut avoir plusieur Book
    // donc a chaque fois qu'on crée un gerne, on declare que la propriété book est un array
    // la methode __contruct () est appelée automaniquement a chaque nouvelle instance de la classe
    // (donc a chaquefois qu'on crée un genre )
    public function __construct()
    {
        $this->books = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    //A la place du setter, on a les methode addBook() et removeBook()
    //books est un array (grace au constructor) donc il faut pouvoir
    // ajouter / supprimer un Book, sans modifier tous les autres
    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setGenre($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getGenre() === $this) {
                $book->setGenre(null);
            }
        }

        return $this;
    }

}
