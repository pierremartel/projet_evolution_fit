<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function showUser(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        $recentUsers = $userRepository->findBy([],['createdAt' => 'DESC'],5);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'recentUsers' => $recentUsers
        ]);
    }
}