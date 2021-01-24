<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @Route("/locale/{_locale}", name="change_lang")
     */
    public function changeLang(Request $request,$_locale)
    {
        $route = $request->get('_route');
        return $this->redirectToRoute($route,['_locale' => $_locale]);
    }
    /**
     * @Route("/{_locale}", name="main")
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(TranslatorInterface $translator,  $_locale): Response
    {
        return $this->render('main/index.html.twig', []);
    }
}
