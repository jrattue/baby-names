<?php

namespace App\Controller;

use App\Entity\Name;
use App\Repository\YearRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/top-names/{gender}")
     */
    public function topNames(YearRepository $repo, string $gender): Response
    {
        $top = [];

        $years = range(2019, 1996, -1);

        foreach ($years as $year){
            $top[$gender][$year] = $repo->getTop($gender, $year);
        }

        return $this->render('default/top.html.twig', [
            'top' => $top
        ]);
    }

    /**
     * @Route("/details/{id}")
     */
    public function name(Name $name): Response
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
            'topYear' => ($topYear ? $topYear->getYear(): null)
        ]);
    }

    /**
     * @Route("/names-for-letter/{letter}")
     * @Route("/names-for-letter")
     */
    public function namesForLetter(YearRepository $repo, string $letter='a'): Response
    {
        $year = 2019;
        $letters = range('a', 'z');
        if(!in_array($letter, $letters)) throw $this->createNotFoundException('Not a letter');

        $genders = [Name::GENDER_MALE, Name::GENDER_FEMALE];
        $names = [];
        foreach ($genders as $gender) {
            $names[$gender] = $repo->getTopForLetter($letter, $year, $gender);
        }

        return $this->render('default/name_for_letter.html.twig', [
            'result' => $names,
            'year' => $year,
            'currentLetter' => $letter,
            'letters' => $letters,
        ]);
    }

}
