<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\LanguageService;

/**
 * UsersManage controller.
 *
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{
    #[Route('/{_locale}', name: 'category')]
    public function index($_locale, LanguageService $LanguageService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $lang = $LanguageService->getLang($em, $_locale);
        $categoryData = $em->getRepository(Category::class)->findBy(['language' => $lang]);

        return $this->render('category/index.html.twig', [
            'categoryData' => $categoryData
        ]);
    }

    #[Route('/{_locale}/new', name: 'category_new')]
    public function categoryNew(Request $request): Response
    {
        $newCategory = new Category();
        $form = $this->createForm(CategoryType::class, $newCategory);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($newCategory);
                $em->flush();
                $this->addFlash('success', 'Dodano Kategorie');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }
            if ($form->get('save')->isClicked()) {
                return $this->redirectToRoute('category');
            } elseif ($form->get('save_add_next')->isClicked()) {
                return $this->redirectToRoute('admin_category_new');
            }
        }
        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/edit/{id}", name="category_edit")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function editCategory(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $userId = $this->getUser()->getId();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $category->setModificatedAt(new \DateTime());
                $em->persist($category);
                $em->flush();
                $this->addFlash('success', 'Zedytowano Kategorie');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
            }

            return $this->redirectToRoute('category');
        }
        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/details/{id}", name="category_details")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsCategory($id)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryData = $em->getRepository(Category::class)->findOneBy(['id' => $id]);

        return $this->render('category/details.html.twig', [
            'categoryData' => $categoryData,
        ]);
    }

    /**
     * @Route("/{_locale}/copy/{id}", name="category_copy")
     */
    public function copyCategory($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($id);
        $newCategory = clone $category;

        try {
            $em->persist($newCategory);
            $em->flush();
            $this->addFlash('success', 'Skopiowano kategorie');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('category');
    }

    /**
     * @Route("/{_locale}/delete/{id}", name="category_delete")
     * @param Request $request
     * $return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCategory($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository(Category::class)->find($id);
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Usunięto kategorie');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd podczas usuwania');
        }
        return $this->redirectToRoute('category');
    }

    /**
     * @Route("/selling_item/set_visibility/{id}{visibility}", name="category_set_visibility")
     */
    public function makeVisible($id, $visibility)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository(Category::class)->find($id);
            $category->setPublication($visibility);
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Zaktulizowano widoczność');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('category');
    }
}
