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

    #[Route('/{_locale}/new', name: 'admin_category_new')]
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

            return $this->redirectToRoute('category');
        }
        return $this->render('category/new.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }
}
