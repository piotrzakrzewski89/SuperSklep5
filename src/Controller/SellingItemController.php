<?php

namespace App\Controller;

use App\Entity\SellingItem;
use App\Form\SellingItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * SellingItem controller.
 *
 * @Route("/admin/selling_item")
 */
class SellingItemController extends AbstractController
{
    #[Route('/{_locale}/', name: 'selling_item')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sellingItemData = $em->getRepository(SellingItem::class)->findAll();

        return $this->render('selling_item/index.html.twig', [
            'sellingItemData' => $sellingItemData
        ]);
    }

    #[Route('/{_locale}/new', name: 'admin_language_new')]
    public function categoryNew(Request $request): Response
    {
        $newSellingItem = new SellingItem();
        $form = $this->createForm(SellingItemType::class, $newSellingItem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($newSellingItem);
                $em->flush();
                $this->addFlash('success', 'Dodano Język');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }

            return $this->redirectToRoute('language');
        }
        return $this->render('selling_item/new.html.twig', [
            'sellingItemForm' => $form->createView(),
        ]);
    }
}
