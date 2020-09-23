<?php

namespace App\Controller;

use App\Entity\Name;
use App\Entity\Year;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/top-names/{gender}")
     */
    public function topNames(EntityManagerInterface $em, $gender)
    {
        $top = [];

        $years = range(2019, 1996, -1);

        foreach ($years as $year){
            $top[$gender][$year] = $em->getRepository(Year::class)->getTop($gender, $year);
        }

        return $this->render('default/top.html.twig', [
            'top' => $top
        ]);
    }

    /**
     * @Route("/details/{id}")
     */
    public function name(Name $name)
    {
        $topYear = null;

        foreach ($name->getYears() as $year){
            if($year->getRank() > 0) {
                if (!$topYear) $topYear = $year;
                if ($year->getRank() < $topYear->getRank()) $topYear = $year;
            }
        }

        return $this->render('default/name.html.twig', [
            'name' => $name,
            'topYear' => $topYear->getYear()
        ]);
    }

    /**
     * @Route("/names-for-letter/{letter}")
     * @Route("/names-for-letter")
     */
    public function namesForLetter(EntityManagerInterface $em, $letter='a')
    {
        $year = 2019;
        $letters = range('a', 'z');
        if(!in_array($letter, $letters)) throw $this->createNotFoundException('Not a letter');

        $genders = [Name::GENDER_MALE, Name::GENDER_FEMALE];
        $names = [];
        foreach ($genders as $gender) {
            $names[$gender] = $em->getRepository(Year::class)->getTopForLetter($letter, $year, $gender);
        }

        return $this->render('default/name_for_letter.html.twig', [
            'result' => $names,
            'year' => $year,
            'currentLetter' => $letter,
            'letters' => $letters,
        ]);
    }

}
