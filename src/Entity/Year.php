<?php

namespace App\Entity;

use App\Repository\YearRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=YearRepository::class)
 */
class Year
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"front_end"})
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"front_end"})
     */
    private $count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"front_end"})
     */
    private $rank;

    /**
     * @ORM\ManyToOne(targetEntity=Name::class, inversedBy="years")
     * @ORM\JoinColumn(nullable=false)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getName(): ?Name
    {
        return $this->name;
    }

    public function setName(?Name $name): self
    {
        $this->name = $name;

        return $this;
    }
}
