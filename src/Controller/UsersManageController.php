<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

/**
 * UsersManage controller.
 *
 * @Route("/admin/users_manage")
 */
class UsersManageController extends AbstractController
{
    #[Route('/{_locale}', name: 'users_manage')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $usersData = $em->getRepository(User::class)->findAll();

        return $this->render('users_manage/index.html.twig', [
            'usersData' => $usersData,
        ]);
    }

    /**
     * @Route("/users_manage/set_visibility/{id}{visibility}", name="users_manage_set_visibility")
     */
    public function makeVisible($id, $visibility)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($id);
            $user->setIsVerified($visibility);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Zaktulizowano widoczność');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Wystąpił nieoczekiwany błąd');
        }
        return $this->redirectToRoute('users_manage');
    }
}
