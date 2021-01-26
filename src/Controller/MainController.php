<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\LanguageService;
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
    public function index($_locale, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $categoryData = $em->getRepository(Category::class)->findBy(['language' => $lang]);

        return $this->render('main/index.html.twig', [
            'categoryData' => $categoryData
        ]);
    }
}
