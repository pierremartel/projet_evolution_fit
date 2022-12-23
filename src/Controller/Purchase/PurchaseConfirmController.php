<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Services\CartService;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmController extends AbstractController
{

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer votre commande")
     */
    public function confirm(Request $request, EntityManagerInterface $em, CartService $cartService)
    {
        $user = $this->getUser();
        date_default_timezone_set('Europe/Paris');
        

        $cartItems = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();

        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // Si aucun article dans le panier
        if(count($cartItems) === 0) {
            $this->addFlash('danger', 'Vous ne pouvez confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        if($form->isSubmitted() && $form->isValid()) {

            // On se créer une purchase ( une commande )
            /** @var Purchase */
            $purchase = $form->getData();

            // On lie la purchase à l'user actuellement connecté et on ajoute la date de création de la purchase
            $purchase->setUser($user);
            $purchase->setPurchasedAt( new \DateTime);
            $purchase->setTotal($total);

            $em->persist($purchase);

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

                $em->persist($purchaseItem);
            }

            $em->flush();

            $cartService->empty();

            $this->addFlash('success', 'La commande a bien été enregistrée');
        
        }

        return $this->render('purchase/purchaseCheckout.html.twig', [
            'form' => $form->createView(),
            'items' => $cartItems,
            'total' => $total
        ]);

    }
}