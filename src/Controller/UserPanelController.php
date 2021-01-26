<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * UserPanel controller.
 *
 * @Route("/user_panel")
 */
class UserPanelController extends AbstractController
{
    #[Route('/{_locale}', name: 'user_panel')]
    public function index(): Response
    {
        return $this->render('user_panel/index.html.twig', []);
    }
}
