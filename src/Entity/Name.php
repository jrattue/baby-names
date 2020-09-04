<?php

namespace App\Entity;

use App\Repository\NameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=NameRepository::class)
 */
class Name
{
    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"front_end"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"front_end"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity=Year::class, mappedBy="name", orphanRemoval=true)
     * @ORM\OrderBy({"year"="DESC"})
     * @Groups({"front_end"})
     */
    private $years;

    public function __construct()
    {
        $this->years = new ArrayCollection();
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
     * @Groups({"front_end"})
     * @SerializedName("gender")
     */
    public function getGenderHuman()
    {
        return $this->getGender() == self::GENDER_MALE ? 'Male' : 'Female';
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

    /**
     * @return Collection|Year[]
     */
    public function getYears(): Collection
    {
        return $this->years;
    }

    public function addYear(Year $year): self
    {
        if (!$this->years->contains($year)) {
            $this->years[] = $year;
            $year->setName($this);
        }

        return $this;
    }

    public function removeYear(Year $year): self
    {
        if ($this->years->contains($year)) {
            $this->years->removeElement($year);
            // set the owning side to null (unless already changed)
            if ($year->getName() === $this) {
                $year->setName(null);
            }
        }

        return $this;
    }
}
