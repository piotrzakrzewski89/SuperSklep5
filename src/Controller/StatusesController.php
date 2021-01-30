<?php

namespace App\Controller;

use App\Entity\Statuses;
use App\Form\StatusesType;
use App\Service\LanguageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Statuses controller.
 *
 * @Route("/admin/statuses")
 */
class StatusesController extends AbstractController
{
    #[Route('/{_locale}', name: 'statuses')]
    public function index($_locale, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $statusesData = $em->getRepository(Statuses::class)->findBy(['language' => $lang]);

        return $this->render('statuses/index.html.twig', [
            'statusesData' => $statusesData
        ]);
    }

    #[Route('/{_locale}/new', name: 'statuses_new')]
    public function newStatuses(Request $request): Response
    {
        $newStatuses = new Statuses();
        $form = $this->createForm(StatusesType::class, $newStatuses);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $newStatuses->setPublication(0);
                $em->persist($newStatuses);
                $em->flush();
                $this->addFlash('success', 'Dodano status');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }
            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('statuses');
            } elseif ($form->get('save_add_next')->isClicked()) {
                return $this->redirectToRoute('statuses_new');
            }
        }
        return $this->render('statuses/new.html.twig', [
            'statusesForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/edit/{id}", name="statuses_edit")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function editStatuses(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Statuses::class)->find($id);
        $form = $this->createForm(StatusesType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->persist($category);
                $em->flush();
                $this->addFlash('success', 'Zedytowano status');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }

            return $this->redirectToRoute('statuses');
        }
        return $this->render('statuses/new.html.twig', [
            'statusesForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/details/{id}", name="statuses_details")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsStatusese($id)
    {
        $em = $this->getDoctrine()->getManager();
        $statusesData = $em->getRepository(Statuses::class)->findOneBy(['id' => $id]);

        return $this->render('statuses/details.html.twig', [
            'statusesData' => $statusesData,
        ]);
    }

    /**
     * @Route("/{_locale}/copy/{id}", name="statuses_copy")
     */
    public function copyStatuses($id)
    {
        $em = $this->getDoctrine()->getManager();
        $statuses = $em->getRepository(Statuses::class)->find($id);
        $newStatuses = clone $statuses;

        try {
            $em->persist($newStatuses);
            $em->flush();
            $this->addFlash('success', 'Skopiowano status');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('statuses');
    }

    /**
     * @Route("/{_locale}/delete/{id}", name="statuses_delete")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteStatuses($id)
    {
        //try {
        $em = $this->getDoctrine()->getManager();
        $statuses = $em->getRepository(Statuses::class)->find($id);
        $em->remove($statuses);
        $em->flush();
        //   $this->addFlash('success', 'Usunięto status');
        //   } catch (\Exception $e) {
        //$this->addFlash('error', 'Wystąpił nieoczekiwany błąd podczas usuwania');
        //      }
        return $this->redirectToRoute('statuses');
    }

    /**
     * @Route("/statuses/set_visibility/{id}{visibility}", name="statuses_set_visibility")
     */
    public function makeVisible($id, $visibility)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $statuses = $em->getRepository(Statuses::class)->find($id);
            $statuses->setPublication($visibility);
            $em->persist($statuses);
            $em->flush();
            $this->addFlash('success', 'Zaktulizowano widoczność');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('statuses');
    }
}
