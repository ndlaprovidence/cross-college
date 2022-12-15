<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[ORM\Table(name:"tbl_race")]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Ranking::class, orphanRemoval: true)]
    private Collection $rankings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $importfilename = null;

    public function __construct()
    {
        $this->rankings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Ranking>
     */
    public function getRankings(): Collection
    {
        return $this->rankings;
    }

    public function getImportfilename(): ?string
    {
        return $this->importfilename;
    }

    public function setImportfilename(?string $importfilename): self
    {
        $this->importfilename = $importfilename;

        return $this;
    }
}
