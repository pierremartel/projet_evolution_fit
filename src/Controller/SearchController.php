<?php

namespace App\Controller;


use App\Form\PropertySearchType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{

    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    public function searchbar(UrlGeneratorInterface $urlGenerator)
    {

        $form = $this->createForm(PropertySearchType::class, null, [
            'action' => $urlGenerator->generate('search_result')
        ]);
        
        return $this->render('search/searchBar.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/search", name="search_result")
     */
    public function result(Request $request)
    {
        // $searchResult = [];
        $search = "";
        
        // if($search !== " "){
        //     return $this->redirectToRoute('home_index');
        // }else { 
            // }
            $search = $request->request->get('property_search')['name'];
        
        
        if($search){
            $searchResult = $this->productRepository->findProductByName($search);
        }

        return $this->render('search/searchResult.html.twig', [
            'searchResult' => $searchResult,
            'search' => $search
        ]);
    }

}
