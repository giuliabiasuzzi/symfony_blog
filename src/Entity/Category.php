<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * countrainte sur la unicité (validation qui peut être jeré sur plusieurs champs
 * @UniqueEntity(fields={"name"}, message="Cette categorie existe déjà")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=20, unique=true)
     * on ajoute , unique=true pour pas avoir
     * deux categories avec le meme nom
     *
     * Validations :
     * le champ ne doit pas être vide >>
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * validation sur la taille
     * @Assert\Length(max="20",
     *     maxMessage="Le nom ne doit pas dépasser {{ limit }} caractères")
     *
     */
    private $name;

    public function __toString()
    {
        return $this->name;
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
}
