<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    
    public function __construct(ProductRepository $productRepository, PaginatorInterface $paginator)
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
    }


    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(EntityManagerInterface $em, SluggerInterface $slugger, Request $request) 
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // On récupère le contenu de l'image passée dans le formulaire
            $picture = $form->get('picture')->getData();

            if ($picture) {
                // On crée le nom du fichier pour éviter doublon
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                // On crée un slug associé à l'$originalFilename
                $safeFilename = $slugger->slug($originalFilename);
                // On reprend les 2étapes précédente, on ajoute un id unique et enfin l'extension du fichier
                $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();

                // Le fichier crée est déplacé vers le dossier où sont stockés les images
                try {
                    $picture->move(
                        $this->getParameter('picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    "Le fichier de stockage des images n'est pas définit";
                }
                // Mis à jour de la propriété picture
                $product->setPicture($newFilename);
            }

            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', "Le produit a bien été crée");

            // MODIFIER LA REDIRECTION VERS PAGE PRODUIT
            return $this->redirectToRoute('product_shop');
        }
        

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'slugger' => $product->getSlug(),
            'product' => $product
        ]);
    }


    /**
     * @Route("/admin/product/update/{id}", name="product_update", requirements={"id":"\d+"})
     */
    public function update($id, EntityManagerInterface $em, Request $request,
                            SluggerInterface $slugger) 
    {
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit n°$id n'existe pas");
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // On récupère le contenu de l'image passée dans le formulaire
            $picture = $form->get('picture')->getData();

            if ($picture) {
                // On crée le nom du fichier pour éviter doublon
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                // On crée un slug associé à l'$originalFilename
                $safeFilename = $slugger->slug($originalFilename);
                // On reprend les 2étapes précédente, on ajoute un id unique et enfin l'extension du fichier
                $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();

                // Le fichier crée est déplacé vers le dossier où sont stockés les images
                try {
                    $picture->move(
                        $this->getParameter('picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    "Le fichier de stockage des images n'est pas définit";
                }
                // Mis à jour de la propriété picture
                $product->setPicture($newFilename);
            }

            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();

            $this->addFlash('success', "Le produit a bien été mis à jour");

            // MODIFIER LA REDIRECTION VERS PAGE PRODUIT
            return $this->redirectToRoute('product_shop');
        }

        return $this->render('product/update.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/collection", name="product_shop")
     */
    public function shop(Request $request)
    {
        $products = $this->productRepository->findAll();

        // Mise en place de la pagination
        $products = $this->paginator->paginate(
            $products, // Requête contenant les données à paginer (ici nos produits)
            $request->query->getInt('page', 1), //Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            8 //nombre de résultats par page
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
        $products = $this->productRepository->findBy(['status' => 'Nouveauté']);
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
    public function show($slug)
    {

        return $this->render('product/show.html.twig');
    }
}
