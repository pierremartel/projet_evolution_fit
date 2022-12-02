<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/registration", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $em,
                                 UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User(); 
        $user->setRoles(['ROLE_USER']);
        
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
            );

            $this->addFlash('success', 'Votre compte a bien été créé');
            
            $em->persist($user);
            $em->flush();

           return $this->redirectToRoute('security_login');

        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
