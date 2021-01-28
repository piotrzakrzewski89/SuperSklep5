<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\SellingItem;
use App\Form\SellingItemType;
use App\Service\ImagesUploadService;
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

    #[Route('/{_locale}/new', name: 'selling_item_new')]
    public function categoryNew(Request $request,  ImagesUploadService $imageUploadService): Response
    {
        $newSellingItem = new SellingItem();
        $form = $this->createForm(SellingItemType::class, $newSellingItem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $images = $form->get('images')->getData();
            try {
                $em->persist($newSellingItem);
                $em->flush();
                $sellingItemId = $newSellingItem->getId();
                $catalogPath = 'download/' . $sellingItemId . '/';

                foreach ($images as $image) {
                    $newImage = new Images();
                    $newFileNamePhoto = $imageUploadService->uploadNewImage($image, $catalogPath);
                    $newImage->setFilePath($newFileNamePhoto);
                    $newImage->setSellingItem($newSellingItem);
                    $em->merge($newImage);
                    $em->flush();
                    $em->clear();
                }
                $this->addFlash('success', 'Dodano Język');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }
            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('selling_item');
            } elseif ($form->get('save_add_next')->isClicked()) {
                return $this->redirectToRoute('selling_item_new');
            }
        }
        return $this->render('selling_item/new.html.twig', [
            'sellingItemForm' => $form->createView(),
        ]);
    }
}
