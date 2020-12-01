<?php

namespace App\Entity;

use App\Repository\YearRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

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
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"front_end"})
     */
    private ?int $year = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"front_end"})
     */
    private ?int $count = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $rank = null;

    /**
     * @ORM\ManyToOne(targetEntity=Name::class, inversedBy="years")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Name $name = null;

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

    /**
     * @Groups({"front_end"})
     * @SerializedName("rank")
     */
    public function getRankWithoutZero(): ?int
    {
        if($this->rank == 0) return 6000;
        return $this->rank;
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
