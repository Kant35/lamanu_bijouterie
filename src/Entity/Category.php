<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// 3 Contraintes
// Je ne peux pas avoir 2 catégories identique dans ma BDD
// $nom ne peux pas être vide
// $nom doit contenir au minimum 3 caractères

#[UniqueEntity(fields:['name'], message: "Le nom de la catégorie existe déjà")]
/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @UniqueEntity(
 *      fields={"nom"},
 *      message="Le nom de la catégorie existe déjà"
 * )
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez renseigner une catégorie")
     * @Assert\Length(
     *      min=3,
     *      minMessage = "3 caractères minimum"
     * )
     */
    private $nom;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
