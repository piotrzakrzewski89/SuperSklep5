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
                $newLanguage->setPublication(0);
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

    /**
     * @Route("/{_locale}/edit/{id}", name="language_edit")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function editLanguage(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $language = $em->getRepository(Language::class)->find($id);
        $form = $this->createForm(LanguageType::class, $language);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $language->setModificatedAt(new \DateTime());
                $em->persist($language);
                $em->flush();
                $this->addFlash('success', 'Zedytowano język');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }

            return $this->redirectToRoute('language');
        }
        return $this->render('language/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/details/{id}", name="language_details")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsLanguage($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languageData = $em->getRepository(Language::class)->findOneBy(['id' => $id]);

        return $this->render('language/details.html.twig', [
            'languageData' => $languageData,
        ]);
    }

    /**
     * @Route("/{_locale}/copy/{id}", name="language_copy")
     */
    public function copyLanguage($id)
    {
        $em = $this->getDoctrine()->getManager();
        $language = $em->getRepository(Language::class)->find($id);
        $new_language = clone $language;

        try {
            $em->persist($new_language);
            $em->flush();
            $this->addFlash('success', 'Skopiowano język');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('language');
    }

    /**
     * @Route("/{_locale}/delete/{id}", name="language_delete")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLanguage($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $language = $em->getRepository(Language::class)->find($id);
            $em->remove($language);
            $em->flush();
            $this->addFlash('success', 'Usunięto język');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd podczas usuwania');
        }
        return $this->redirectToRoute('language');
    }

    /**
     * @Route("/selling_item/set_visibility/{id}{visibility}", name="language_set_visibility")
     */
    public function makeVisible($id, $visibility)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $sellingItem = $em->getRepository(Language::class)->find($id);
            $sellingItem->setPublication($visibility);
            $em->persist($sellingItem);
            $em->flush();
            $this->addFlash('success', 'Zaktulizowano widoczność');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('language');
    }
}
