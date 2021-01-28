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
                $newSellingItem->setCreatedAt(new \DateTime());
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
                $this->addFlash('success', 'Dodano Przedmiot');
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

    /**
     * @Route("/{_locale}/edit/{id}", name="selling_item_edit")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function editSellingItem(Request $request, $id, ImagesUploadService $imageUploadService)
    {
        $em = $this->getDoctrine()->getManager();
        $sellingItem = $em->getRepository(SellingItem::class)->find($id);
        $form = $this->createForm(SellingItemType::class, $sellingItem);
       // $oldFilePath = $sellingItem->getPhotoPath();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileName = $form->get('photo_path')->getData();
            try {
                $catalogPath = 'download/' . $this->getUser()->getId() . '/';
                if ($pictureFileName != null) {
                    $newFileNamePhoto = $imageUploadService->uploadEditImage($pictureFileName, $oldFilePath, $catalogPath);
                    $sellingItem->setPhotoPath($newFileNamePhoto);
                }
                $sellingItem->setModificatedAt(new \DateTime());
                $em->persist($sellingItem);
                $em->flush();
                $this->addFlash('success', 'Zedytowano post');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }

            return $this->redirectToRoute('selling_item');
        }
        return $this->render('selling_item/new.html.twig', [
            'sellingItemForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/copy/{id}", name="selling_item_copy")
     */
    public function copySellingItem($id)
    {
        $em = $this->getDoctrine()->getManager();
        $sellingItem = $em->getRepository(SellingItem::class)->find($id);
        $newSellingItem = clone $sellingItem;

        try {
            $em->persist($newSellingItem);
            $em->flush();
            $this->addFlash('success', 'Skopiowano przedmiot');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('selling_item');
    }

    /**
     * @Route("/{_locale}/delete/{id}", name="selling_item_delete")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteSellingItem($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $sellingItem = $em->getRepository(SellingItem::class)->find($id);
            $em->remove($sellingItem);
            $em->flush();
            $this->addFlash('success', 'Usunięto przedmiot');
        } catch (\Exception $e) {
            // $this->addFlash('error', 'Wystąpił nieoczekiwany błąd podczas usuwania');
        }
        return $this->redirectToRoute('selling_item');
    }

    /**
     * @Route("/selling_item/set_visibility/{id}{visibility}", name="selling_item_set_visibility")
     */
    public function makeVisible($id, $visibility)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $sellingItem = $em->getRepository(SellingItem::class)->find($id);
            $sellingItem->setModifiedAt(new \DateTime());
            $sellingItem->setPublication($visibility);
            $em->persist($sellingItem);
            $em->flush();
            $this->addFlash('success', 'Zaktulizowano widoczność');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('selling_item');
    }
}
