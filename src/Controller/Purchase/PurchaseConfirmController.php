<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Services\CartService;
use App\Form\CartConfirmationType;
use App\Services\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmController extends AbstractController
{

    protected $purchasePersister;

    public function __construct(PurchasePersister $purchasePersister)
    {
        $this->purchasePersister = $purchasePersister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer votre commande")
     */
    public function confirm(Request $request, EntityManagerInterface $em, CartService $cartService)
    {
        
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

            $this->purchasePersister->storePurchase($purchase);

            return $this->redirectToRoute('Purchase_showCardForm', [
                'id' => $purchase->getId(),
            ]);
        
        }

        return $this->render('purchase/purchaseCheckout.html.twig', [
            'form' => $form->createView(),
            'items' => $cartItems,
            'total' => $total
        ]);

    }
}