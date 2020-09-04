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
     * @Route("/top-names")
     */
    public function topNames(EntityManagerInterface $em)
    {
        $top = [];

        $years = range(2019, 1996, -1);
        $genders = [Name::GENDER_MALE, Name::GENDER_FEMALE];

        foreach ($years as $year){
            foreach ($genders as $gender){
                $top[$gender][$year] = $em->getRepository(Year::class)->getTop($gender, $year);
            }
        }

        return $this->render('default/top.html.twig', [
            'top' => $top
        ]);
    }

    /**
     * @Route("/details/{name}")
     */
    public function name(Name $name)
    {
        return $this->render('default/name.html.twig', [
            'name' => $name
        ]);
    }
}
