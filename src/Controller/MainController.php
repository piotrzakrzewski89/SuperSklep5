<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('main', ['_locale' => 'pl']);
    }

    /**
     * @Route("/{_locale}", name="main")
     * @return Response
     */
    public function index($_locale): Response
    {
        return $this->render('main/index.html.twig', []);
    }
}
