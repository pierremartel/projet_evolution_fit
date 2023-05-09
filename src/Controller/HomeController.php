<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     */
    public function index(ProductRepository $productRepository, Request $request,
                            EntityManagerInterface $em): Response
    {
        $products = $productRepository->findBy(['event' => 'Nouveauté'],[],8);

        //Request for newsletter

        //1 .Récupérer le user
        $user = $this->getUser();

        //2 .Vérifier si la request contient un élément
        //  Vérifier que l'email correspond au user
        if($request->request->count() > 0 ){

            if(!$user){
                return $this->redirectToRoute('security_login');
            }

            if($request->request->get('email') !== $user->getEmail()){
                $this->addFlash('warning', 'Un problème est survenue !');
                return $this->redirectToRoute('home_index');
            }
            //Vérifier si newsletter == true
            if($user->getNewsletter()) {
                $this->addFlash('warning', 'Vous êtes déjà abonné à la newsletter');
                return $this->redirectToRoute('home_index');
            }
            //Si false, modifier la propriété setNewsletter
            $user->setNewsletter(true);
            $em->flush($user);

            $this->addFlash('success', 'Abonnement à la newsletter avec succès !');
        }

        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
