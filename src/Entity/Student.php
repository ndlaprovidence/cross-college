<?php

namespace App\Entity;

use App\Entity\Grade;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Table(name:"tbl_student")]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 1)]
    private ?string $gender = null;

    #[ORM\Column(nullable: true)]
    private ?float $mas = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grade $grade = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Ranking::class, orphanRemoval: true)]
    private Collection $rankings;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $objective = null;

    #[ORM\Column]
    private ?int $Note = null;

    public function __construct()
    {
        $this->rankings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getMas(): ?float
    {
        return $this->mas;
    }

    public function setMas(?float $mas): self
    {
        $this->mas = $mas;

        return $this;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * @return Collection<int, Ranking>
     */
    public function getRankings(): Collection
    {
        return $this->rankings;
    }

    public function addRanking(Ranking $ranking): self
    {
        if (!$this->rankings->contains($ranking)) {
            $this->rankings->add($ranking);
            $ranking->setStudent($this);
        }

        return $this;
    }

    public function removeRanking(Ranking $ranking): self
    {
        if ($this->rankings->removeElement($ranking)) {
            // set the owning side to null (unless already changed)
            if ($ranking->getStudent() === $this) {
                $ranking->setStudent(null);
            }
        }

        return $this;
    }

    public function getObjective(): ?\DateTimeInterface
    {
        return $this->objective;
    }

    public function setObjective(?\DateTimeInterface $objective): self
    {
        $this->objective = $objective;

        return $this;
    }

    public function __toString() {
        return $this->id;
        return $this->objective;
    }

    public function getNote(): ?int
    {
        return $this->Note;
    }

    public function setNote(int $Note): self
    {
        $this->Note = $Note;

        return $this;
    }
}
