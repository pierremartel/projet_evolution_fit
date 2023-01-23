<?php 

namespace App\Services;

use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{   
    protected $em;
    protected $security;
    protected $cartService;
    

    public function __construct(EntityManagerInterface $em, Security $security, CartService $cartService)
    {
        $this->em = $em;
        $this->security = $security;
        $this->cartService = $cartService;
    }

    public function storePurchase(Purchase $purchase)
    {
        date_default_timezone_set('Europe/Paris');

        $cartItems = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();

        // On lie la purchase à l'user actuellement connecté et on ajoute la date de création de la purchase
        $purchase->setUser($this->security->getUser());
        $purchase->setPurchasedAt( new DateTime);
        $purchase->setTotal($total);

        $this->em->persist($purchase);

        // On lie les produits du panier à la purchase (CartService)
        foreach($cartItems as $cartItem){
            // dd($cartItem);
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                        ->setProduct($cartItem['product'])
                        ->setProductName($cartItem['product']->getName())
                        ->setProductPrice($cartItem['product']->getPrice())
                        ->setQuantity($cartItem['qty'])
                        ->setTotal($total);

            $this->em->persist($purchaseItem);
        }

        $this->em->flush();

    }
}