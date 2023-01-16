<?php 

namespace App\Controller;

use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfilController extends AbstractController
{
    
    /**
     * @Route("/account", name="profil_account")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function account(PurchaseRepository $purchaseRepository)
    {
        // 1. Nous devons nous assurer que la personne est connectée (sinon redirect page d'accueil)
        $user = $this->getUser();
        $purchases = $purchaseRepository->findBy(['user' => $user], ['id' => 'DESC']);

        // (= IsGranted)
        // if(!$user){
        //     throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes");
        // }

        // 2. Nous voulons savoir QUI est connectée
        // 3. Nous voulons passer l'utilisateur connecté à twig afin d'afficher ses commandes

        return $this->render('profil/account.html.twig', [
            // 'purchases' => $user->getPurchases(),
            'purchases' => $purchases
        ]);
    }
    
}