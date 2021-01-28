<?php

namespace App\Controller;

use App\Entity\Language;
use App\Form\LanguageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Language controller.
 *
 * @Route("/admin/language")
 */
class LanguageController extends AbstractController
{
    #[Route('/{_locale}/', name: 'language')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $languageData = $em->getRepository(Language::class)->findAll();

        return $this->render('language/index.html.twig', [
            'languageData' => $languageData
        ]);
    }

    #[Route('/{_locale}/new', name: 'language_new')]
    public function categoryNew(Request $request): Response
    {
        $newLanguage = new Language();
        $form = $this->createForm(LanguageType::class, $newLanguage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($newLanguage);
                $em->flush();
                $this->addFlash('success', 'Dodano Język');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }
            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('language');
            } elseif ($form->get('save_add_next')->isClicked()) {
                return $this->redirectToRoute('language_new');
            }
        }
        return $this->render('language/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }
}
