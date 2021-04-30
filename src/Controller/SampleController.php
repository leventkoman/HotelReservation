<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SampleController extends AbstractController
{
    /**
     * @Route("/sample", name="sample")
     */
    public function index(): Response
    {
        return $this->render('sample/index.html.twig', [
            'controller_name' => 'SampleController',
        ]);
    }
}
