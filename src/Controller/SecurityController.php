<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\LoginType;
use App\Services\JwtService;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Services\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    protected $em;
    protected $mailService;
    protected $jwt;
    protected $userRepository;

    public function __construct(EntityManagerInterface $em, SendMailService $mailService,
                                JwtService $jwt, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->mailService = $mailService;
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
    }


    /**
     * @Route("/connexion", name="security_login")
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
     * @Route("/déconnexion", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User(); 
        $user->setRoles(['ROLE_USER']);
        date_default_timezone_set('Europe/Paris');
        
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new DateTime());

            // Hashage du mot de passe de l'utilisateur
            $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
            );

            $this->addFlash('success', 'Votre compte a bien été créé');
            $this->em->persist($user);
            $this->em->flush();

            //On genere le JWT de l'utilisateur
            //On crée le header
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            //On crée le payload
            $payload = ['user_id' => $user->getId()];
            //On genere le Token
            $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


            //On envoie le mail d'activation du compte
            $this->mailService->send(
                    'no-reply@evolution-fit.fr',
                    $user->getEmail(),
                    'Activation de votre compte sur le site EvolutionFit',
                    'register',
                    compact('user', 'token')// compact() remplace un tableau clé => valeur
            );

           return $this->redirectToRoute('security_login');

        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/check/{token}", name="security_check_user")
     */
    public function checkUser($token): Response
    {
        //On verifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if($this->jwt->isValid($token) && !$this->jwt->isExpired($token) && $this->jwt->check($token, 
            $this->getParameter('app.jwtsecret'))) {

                //On récupère le payload
                $payload = $this->jwt->getPayload($token);

                //On recupere le user du token
                $user = $this->userRepository->find($payload['user_id']);

                //On verifie que l'user existe et n'a pas encore activé son compte
                if($user && !$user->getIsVerified()){
                    $user->setIsVerified(true);
                    $this->em->flush($user);
                    $this->addFlash('success', 'Utilisateur activé');
                    return $this->redirectToRoute('security_login');
                }
            }

            //Ici si un probleme se pose dans le token
            $this->addFlash('danger', 'Le token est invalide ou a expiré');
            return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/resend-check-user-active", name="security_resend_check")
     */
    public function resendCheck(): Response
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('security_login');
        }

        if($user->getIsVerified()){
            $this->addFlash('warning', 'Cette utilisateur est déjà activé');
            return $this->redirectToRoute('home_index');
        }

        //On genere le JWT de l'utilisateur
        //On crée le header
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        //On crée le payload
        $payload = ['user_id' => $user->getId()];
        //On genere le Token
        $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));


        //On envoie le mail d'activation du compte
        $this->mailService->send(
             'no-reply@evolution-fit.fr',
             $user->getEmail(),
             'Activation de votre compte sur le site EvolutionFit',
             'register',
             compact('user', 'token')// compact() remplace un tableau clé => valeur
        );
        $this->addFlash('warning', 'Email de vérification envoyé');
        return $this->redirectToRoute('home_index'); 
    }


    /**
     * @Route("/forget-password", name="security_forget_password")
     */
    public function forgetPassword(Request $request, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //On va chercher l'utilisateur par son mail
            $user = $this->userRepository->findOneByEmail($form->get('email')->getData());
            
            //On vérifie si on a un utilisateur
            if(!$user){
                $this->addFlash('danger', 'Un problème est survenue !');
                return $this->redirectToRoute('security_login');
            }

            //On genere un Token de réinitialisation
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);

            $this->em->persist($user);
            $this->em->flush();

            //On genere un lien de réinitialisation du mot de passe
            $url = $this->generateUrl('security_reset_password', ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL);

            //On crée les données du mail
            $context = compact('url', 'user');

            //On envoie le mail
            $this->mailService->send(
                        'no-reply@evoultion-fit.fr',
                        $user->getEmail(),
                        'Réinitialisation du mot de passe',
                        'reset_password',
                        $context
            );

            $this->addFlash('success', 'Email envoyé avec succès !');
            return $this->redirectToRoute('security_login');

        }

        return $this->render('security/reset_password_request.html.twig', [
            'formReset' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forget-password/reset/{token}", name="security_reset_password")
     */
    public function resetPassword($token, Request $request, 
                                 UserPasswordHasherInterface $passwordHasher)
    {
        //On vérifie si l'on a ce token dans la bdd
        $user = $this->userRepository->findOneByResetToken($token);

        if(!$user){
            $this->addFlash('danger', 'Jeton invalide');
            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //On efface le token
            $user->setResetToken('');
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            ));

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('security_login');
         }

        return $this->render('security/reset_password.html.twig', [
            'formPassword' => $form->createView(),
        ]);

    }
}
