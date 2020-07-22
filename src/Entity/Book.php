<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Il manque un title")
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message = "le nombre de pages ne peut pas etre negatif ")
     * @Assert\NotBlank(message = "Ce champ ne peut pas etre vide")
     */
    private $nbPages;


    /**
     * @ORM\Column (type ="text", nullable=true)
     */
    private $resume;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Author;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * je créé une propriété de type string (une colonne varchar) pour stocker le nom de l'image
     * pour chaque livre
     */
    private $bookCover;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     */
    public function setResume($resume): void
    {
        $this->resume = $resume;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    public function setNbPages(int $nbPages): self
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->Author;
    }

    public function setAuthor(?Author $Author): self
    {
        $this->Author = $Author;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getBookCover()
    {
        return $this->bookCover;
    }

    /**
     * @param mixed $bookCover
     */
    public function setBookCover($bookCover): void
    {
        $this->bookCover = $bookCover;
    }

}
