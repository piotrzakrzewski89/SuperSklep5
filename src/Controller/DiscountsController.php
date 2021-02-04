<?php

namespace App\Controller;

use App\Entity\Discounts;
use App\Form\DiscountsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Discounts controller.
 *
 * @Route("/admin/discounts")
 */
class DiscountsController extends AbstractController
{
    #[Route('/{_locale}', name: 'discounts')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $discountsData = $em->getRepository(Discounts::class)->findAll();

        return $this->render('discounts/index.html.twig', [
            'discountsData' => $discountsData
        ]);
    }

    #[Route('/{_locale}/new', name: 'discounts_new')]
    public function newDiscounts(Request $request): Response
    {
        $newdiscount = new Discounts();
        $form = $this->createForm(DiscountsType::class, $newdiscount);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $newdiscount->setPublication(0);
                $em->persist($newdiscount);
                $em->flush();
                $this->addFlash('success', 'Dodano Rabat');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }
            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('discounts');
            } elseif ($form->get('save_add_next')->isClicked()) {
                return $this->redirectToRoute('discounts_new');
            }
        }
        return $this->render('discounts/new.html.twig', [
            'discountForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/edit/{id}", name="discounts_edit")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function editDiscounts(Request $request, $id)
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

            return $this->redirectToRoute('discounts');
        }
        return $this->render('language/new.html.twig', [
            'languageForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/details/{id}", name="discounts_details")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsDiscounts($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languageData = $em->getRepository(Language::class)->findOneBy(['id' => $id]);

        return $this->render('language/details.html.twig', [
            'languageData' => $languageData,
        ]);
    }

    /**
     * @Route("/{_locale}/copy/{id}", name="discounts_copy")
     */
    public function copyDiscounts($id)
    {
        $em = $this->getDoctrine()->getManager();
        $discounts = $em->getRepository(Discounts::class)->find($id);
        $newDiscounts = clone $discounts;

        try {
            $em->persist($newDiscounts);
            $em->flush();
            $this->addFlash('success', 'Skopiowano język');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('language');
    }

    /**
     * @Route("/{_locale}/delete/{id}", name="discounts_delete")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteDiscounts($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $discounts = $em->getRepository(Language::class)->find($id);
            $em->remove($discounts);
            $em->flush();
            $this->addFlash('success', 'Usunięto język');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd podczas usuwania');
        }
        return $this->redirectToRoute('language');
    }

    /**
     * @Route("/discounts/set_visibility/{id}{visibility}", name="discounts_set_visibility")
     */
    public function makeVisible($id, $visibility)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $discounts = $em->getRepository(Discounts::class)->find($id);
            $discounts->setPublication($visibility);
            $em->persist($discounts);
            $em->flush();
            $this->addFlash('success', 'Zaktulizowano widoczność');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('discounts');
    }
}
