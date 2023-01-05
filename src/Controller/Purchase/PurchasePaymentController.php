<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Services\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="Purchase_showCardForm", requirements={"id": "\d+"})
     * @IsGranted("ROLE_USER", message="Vous ne pouvez accéder à cette page si le formulaire de commande n'est pas remplie")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository, CartService $cartService)
    {
        $cartItems = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();

        $purchase = $purchaseRepository->find($id);

        if(
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ){
            return $this->redirectToRoute('cart_show');
            }
           

        \Stripe\Stripe::setApiKey('sk_test_51MIBHdDi8X1ZgKLhlzWUUKeT6F01qK7vIdT2mb7Xdxni3SQxiBbMNHQQ05fxceg1q4zlMDJ4IiR5lPNjiKehmMjT00mOkDErdk');

        // Create a PaymentIntent with amount and currency
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal()*100,
            'currency' => 'eur',
        ]);


        // On envoie à la vue la donnée "client_secret" contenue dans l'objet $paymentIntent
        // nécessaire pour stripe pour définir l'id de ce payement crée

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'purchase' => $purchase,
            'items' => $cartItems,
            'total' => $total
        ]);

    }
    
}
