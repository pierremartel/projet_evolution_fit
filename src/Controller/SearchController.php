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
        $search = "";
        
        //Si l'user va sur la route '/search' sans passer par la barre de recherche
        if($request->request->get('property_search') > 0){
            $search = $request->request->get('property_search')['name'];
        }else {
            $this->addFlash('warning', 'Une erreur est survenue');
            return $this->redirectToRoute('home_index');
        }
        
        //Si la recherche contient un élément, on renvoie cet élément, 
        //sinon on renvoie tout les produits
        if($search){
            $searchResult = $this->productRepository->findProductByName($search);
        }else {
            $searchResult = $this->productRepository->findAll();
        }

        return $this->render('search/searchResult.html.twig', [
            'searchResult' => $searchResult,
            'search' => $search
        ]);
    } 

}
