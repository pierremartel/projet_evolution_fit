<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{

    protected $productRepository;

    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/search", name="app_search")
     */
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
        ]);
    }


    // /**
    //  * @Route("/searchbar/{research}", name="search_searchbar")
    //  */
    // public function searchbar(string $research)
    // {
    //     $form = $this->createForm(SearchType::class, null, [
    //         // 'action' => $this->generateUrl('handleSearch')
    //     ]);
    //     $products = $this->productRepository->findAll();


    //     return $this->render('search/searchBar.html.twig', [
    //         // 'form' => $form->createView(),
    //         'products' => $products
    //     ]);
    // }

}
