<?php 

namespace App\Controller\Purchase;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseListController extends AbstractController
{
    
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function index()
    {
        // 1. Nous devons nous assurer que la personne est connectée (sinon redirect page d'accueil)
        $user = $this->getUser();

        // (= IsGranted)
        // if(!$user){
        //     throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes");
        // }

        // 2. Nous voulons savoir QUI est connectée
        // 3. Nous voulons passer l'utilisateur connecté à twig afin d'afficher ses commandes
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);

    }

    
}