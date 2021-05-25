<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage_index")
     * @return Response
     */
    public function index()
    {
        return $this->render('homepage/index.html.twig');
    }
}