<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;

class FilterService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    public function sortBy(Request $request)
    {
        $value = '';

        if($request->query->has('sort_product')){
            if($request->query->get('sort_product') == 'titre-ascendant'){
                $products = $this->productRepository->findProductByNameAsc($value);  
            }elseif($request->query->get('sort_product') == 'titre-descendant'){
                $products = $this->productRepository->findProductByNameDesc($value); 
            } 

            if($request->query->get('sort_product') == 'prix-ascendant'){
                $products = $this->productRepository->findProductByPriceAsc($value);  
            }elseif($request->query->get('sort_product') == 'prix-descendant'){
                $products = $this->productRepository->findProductByPriceDesc($value); 
            } 
        }
    }    
}