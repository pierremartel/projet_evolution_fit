<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Services\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{

    /**
     * @Route("/purchase/success/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, 
                            EntityManagerInterface $em, CartService $cartService)
    {
        // 1. Je récupère ma commande

        $purchase = $purchaseRepository->find($id);

        if(
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ){
            $this->addFlash('danger', "La commande n'existe pas");
            return $this->redirectToRoute('purchase_index');
        }

        // 2. Je la fait passer au status Payée ( = PAID)
        $purchase->setStatus(Purchase::STATUS_PAID);
        $purchase->setTotal($purchase->getTotal() + ($purchase->getPurchaseShipping()->getPrice()));
        $em->flush();

        // 3. Je vide le panier
        $cartService->empty();

        // 4. Je redirige le user vers une page de succès
        return $this->render('purchase/successPayment.html.twig');
    }
}