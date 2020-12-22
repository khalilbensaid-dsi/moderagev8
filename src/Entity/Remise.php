<?php

namespace App\Entity;

use App\Repository\RemiseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RemiseRepository::class)
 */
class Remise
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $pourcentage;

    /**
     * @ORM\OneToOne(targetEntity=Sondage::class, mappedBy="remise", cascade={"persist", "remove"})
     */
    private $sondage;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getSondage(): ?Sondage
    {
        return $this->sondage;
    }

    public function setSondage(?Sondage $sondage): self
    {
        // unset the owning side of the relation if necessary
        if ($sondage === null && $this->sondage !== null) {
            $this->sondage->setRemise(null);
        }

        // set the owning side of the relation if necessary
        if ($sondage !== null && $sondage->getRemise() !== $this) {
            $sondage->setRemise($this);
        }

        $this->sondage = $sondage;

        return $this;
    }

    
}
