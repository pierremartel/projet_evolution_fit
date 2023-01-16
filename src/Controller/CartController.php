<?php

namespace App\Controller;

use App\Form\ChoiceAttrType;
use App\Services\CartService;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    protected $product;
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id": "\d+"})
     */
    public function add($id, Request $request)
    {
        // Securisation (voir si le produit existe)
        $product = $this->productRepository->find($id);

        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        $form = $this->createForm(ChoiceAttrType::class);
        $form->handleRequest($request);

        // $size = "M";
        // On récupère la taille ($size) du produit dans l'url
        $size = $request->query->get('size');
        if($form->isSubmitted() && $form->isValid()) {
            $size = $form["size"]->getData();
            
            // dd($size);
        }
        

        $this->cartService->add($id, $size);
        
        $this->addFlash('success', 'Le produit a bien été ajouté au panier !');

        if ($request->query->get('size')){
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'category' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),
        ]);

    }


    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        $detailedCart = $this->cartService->getDetailedCartItems();

        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
     */
    public function delete($id, Request $request)
    {
        // On récupère la taille ($size) du produit dans l'url
        $size = $request->query->get('size');

        $product = $this->productRepository->find($id);
        
        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut être supprimé !");
        }

        // $sizes = $this->cartService->getDetailedCartItems();
        // dd($sizes);
        // foreach($sizes as $size){
            
            // $size = $size['size'];
        // }

        $this->cartService->remove($id, $size);
        $this->addFlash("success", "Le produit a bien été supprimé du panier !");

        return $this->redirectToRoute("cart_show");

    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
     */
    public function decrement($id, Request $request)
    {
        $product = $this->productRepository->find($id);
        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut être décrémenté !");
        }

        // On récupère la taille ($size) du produit dans l'url
        $size = $request->query->get('size');
        $this->cartService->decrement($id, $size);

        return $this->redirectToRoute("cart_show");

    }

}
