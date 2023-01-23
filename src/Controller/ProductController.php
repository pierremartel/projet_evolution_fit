<?php

namespace App\Controller;


use App\Form\SearchType;
use App\Model\SearchData;
use App\Entity\ProductAttr;
use App\Form\ChoiceAttrType;
use App\Services\FilterService;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductAttrRepository;
use App\Repository\ProductSizeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductController extends AbstractController
{
    protected $productRepository;
    protected $paginator;
    protected $filterService;
    
    public function __construct(ProductRepository $productRepository, PaginatorInterface $paginator,
                                FilterService $filterService)
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
        $this->filterService = $filterService;
    }

    /**
     * @Route("/collection", name="product_shop")
     */
    public function shop(Request $request)
    {
        
        $products = $this->productRepository->findAll();

        //Mise en place du système de filtre
        $value = '';
        //Je vérifie si l'on a un élément dans la requête
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

        // Mise en place de la pagination
        $products = $this->paginator->paginate(
            $products, // Requête contenant les données à paginer (ici nos produits)
            $request->query->getInt('page', 1), //Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            20 //nombre de résultats par page
        );

        return $this->render('product/shop.html.twig', [
            'products' => $products
        ]);
    }


    /**
     * @Route("/news", name="product_news")
     */
    public function news(Request $request)
    {
        $products = $this->productRepository->findBy(['event' => 'Nouveauté']);
         // Mise en place de la pagination
         $products = $this->paginator->paginate(
            $products, // Requête contenant les données à paginer (ici nos produits)
            $request->query->getInt('page', 1), //Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            8 //nombre de résultats par page
        );

        return $this->render('product/news.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/collection/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository, Request $request)
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        $products = $this->productRepository->findBy(['category' => $category]);

        if(!$category){
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        //Mise en place du système de filtre
        //Je vérifie si l'on a un élément dans la requête
        if($request->query->has('sort_product')){
            if($request->query->get('sort_product') == 'titre-ascendant'){
                $products = $this->productRepository->findBy(['category' => $category], ['name' => 'ASC']);  
            }elseif($request->query->get('sort_product') == 'titre-descendant'){
                $products = $this->productRepository->findBy(['category' => $category], ['name' => 'DESC']);  
            } 

            if($request->query->get('sort_product') == 'prix-ascendant'){
                $products = $this->productRepository->findBy(['category' => $category], ['price' => 'ASC']);   
            }elseif($request->query->get('sort_product') == 'prix-descendant'){
                $products = $this->productRepository->findBy(['category' => $category], ['price' => 'DESC']);   
            } 
        }        

        // Mise en place de la pagination
        $products = $this->paginator->paginate(
            $products, // Requête contenant les données à paginer (ici nos produits)
            $request->query->getInt('page', 1), //Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            8 //nombre de résultats par page
        );

        return $this->render('product/category.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }


    /**
     * @Route("/collection/{category}/{slug}", name="product_show")
     */
    public function show($slug, ProductAttrRepository $productAttrRepository
                        )
    {
        $product = $this->productRepository->findOneBy(['slug' => $slug]);
        
        $productAttrs = $productAttrRepository->findBy(['product' => $product]);


        if(!$product){
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $form = $this->createForm(ChoiceAttrType::class, null, [
            'action' => $this->generateUrl('cart_add', ['id' => $product->getId()]),
            // 'p' => $product->getId()
        ]);
        
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'productAttrs' => $productAttrs,
            'form' => $form->createView(),
        ]);
    }
    
}
