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
}
