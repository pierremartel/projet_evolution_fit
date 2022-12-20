<?php 

namespace App\Services;

use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;
    protected $productSizeRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository,
                                ProductSizeRepository $productSizeRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->productSizeRepository = $productSizeRepository;
    }

    public function add(int $id, $size) {
        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. S'il n'existe pas encore, alors prendre un tableau vide 

        $cart = $this->session->get('cart', []);
    
        // 3. Voir si le produit($id) existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon ajouter le produit avec la quantité 1

        
        
        // $cart[$id] = array();

        // if(is_array($cart[$id]))
        // {

            if (array_key_exists($id, $cart) && array_key_exists($size, $cart[$id])){
                $cart[$id][$size]++; 
            } else{
                $cart[$id][$size] = 1;
            }

        // }
        
        // dd($cart);
        // 6. Enregistrer le tableau mis à jour dans la session

        $this->session->set('cart', $cart);
        // $this->session->remove('cart');

    }
    

    public function getTotal(): int {
        $total = 0;

        foreach($this->session->get('cart', []) as $id => $content) {
            $product = $this->productRepository->find($id);

            if(!$product){
                continue;
            }

            foreach($content as $size=> $qty){

            $total += ($product->getPrice() * $qty);
            }
        }
        return $total;

    }


    public function getDetailedCartItems(): array {
        $detailedCart = [];
        
        foreach($this->session->get('cart', []) as $id => $content) {
            $product = $this->productRepository->find($id);

            if(!$product){
                continue;
            }
            // dd($qty);
            foreach($content as $size=> $qty){
                // $productSize = $this->productSizeRepository->find($id);
                // dd($id);
                
                $detailedCart[] = [
                    'product' => $product,
                    'size' => $size,
                    'qty' => $qty,
                ];
            }
        }

        // dd($detailedCart);
        return $detailedCart;
    }


    public function remove(int $id, $size) 
    {

        $cart = $this->session->get('cart', []);
        // dd($size);
        if(array_key_exists($id, $cart)){

            unset($cart[$id][$size]);
        }

        $this->session->set('cart', $cart);
    }


    public function decrement(int $id) 
    {

        $cart = $this->session->get('cart', []);
        
        if(!array_key_exists($id, $cart)){
            return;
        }

        // Soit le produit est à 1, alors il faut simplement le supprimer

        if($cart[$id] == 1){
            // $this->remove($id);
            return;
        }

        // Soit le produit est à plus de 1, alors il faut le décrémenter

        $cart[$id]--;

        $this->session->set('cart', $cart);
    }

}