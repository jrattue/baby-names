<?php

namespace App\Controller;

use App\Entity\Name;
use App\Repository\NameRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @Route("/api", name="api")
 */
class ApiController extends AbstractRestController
{

    /**
     * @Route("/search/{term}", methods={"GET"}, format="json")
     */
    public function index(string $term, NameRepository $repository): Response
    {
        $result = $repository->findForSearch($term);
        return $this->handleView($this->createView($result));
    }

    /**
     * @Route("/details/{id}", methods={"GET"}, format="json")
     */
    public function details(Name $name): Response
    {
        return $this->handleView($this->createView($name));
    }

}
