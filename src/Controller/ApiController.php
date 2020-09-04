<?php

namespace App\Controller;

use App\Entity\Name;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @Route("/api", name="api")
 */
class ApiController extends AbstractRestController
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/search/{term}", methods={"GET"}, format="json")
     */
    public function index($term)
    {
        $result = $this->em->getRepository(Name::class)->findForSearch($term);
        return $this->handleView($this->createView($result));
    }

    /**
     * @Route("/details/{id}", methods={"GET"}, format="json")
     */
    public function details(Name $name)
    {
        return $this->handleView($this->createView($name));
    }

}
