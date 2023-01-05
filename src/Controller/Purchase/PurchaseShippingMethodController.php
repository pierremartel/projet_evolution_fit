<?php

namespace App\Controller\Purchase;

use App\Services\CartService;
use App\Form\ShippingMethodType;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseShippingMethodController extends AbstractController
{

    /**
     * @Route("purchase/shipping-method/{id}", name="purchase_shipping_method", requirements={"id": "\d+"})
     * @IsGranted("ROLE_USER", message="Vous ne pouvez accéder à cette page si le formulaire de commande n'est pas remplie")
     */
    public function shippingMethod($id, PurchaseRepository $purchaseRepository,
                                    CartService $cartService)
    {
        $cartItems = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();

        $purchase = $purchaseRepository->find($id);

        if(
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser())
        ){
            return $this->redirectToRoute('cart_show');
            }

        $form = $this->createForm(ShippingMethodType::class);

        return $this->render('purchase/shippingMethod.html.twig', [
            'items' => $cartItems,
            'total' => $total,
            'purchase' => $purchase,
            'form' => $form->createView()
        ]);
    }

}