<?php

namespace App\Entity;

use App\Entity\Article;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @Table(name="pictures")
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picturePath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pictureName;

    /**
     * @ORM\OneToOne(targetEntity=Article::class, inversedBy="picture", cascade={"persist", "remove"})
     */
    private $article;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(string $pictureName): self
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    
}
