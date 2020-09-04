<?php

namespace App\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class AbstractRestController extends AbstractFOSRestController
{

    public function createView($data, $groups = ['front_end']): View
    {
        $context = new Context();
        $view = $this->view($data);
        $context->setGroups($groups);
        $view->setContext($context);
        return $view;
    }

    protected function createValidationErrorsResponse(ConstraintViolationList $validationErrors, int $statusCode = null): Response
    {
        $view = $this->createView( $validationErrors);
        $view
            ->setStatusCode($statusCode ?? 400)
            ->setFormat('json');

        return $this->handleView($view);
    }


}