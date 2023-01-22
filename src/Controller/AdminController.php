<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\ProductAttr;
use App\Form\ProductAttrType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductAttrRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    protected $em;
    protected $productRepository;
    protected $slugger;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $em,
                                SluggerInterface $slugger)
    {
        $this->productRepository = $productRepository;
        $this->em = $em;
        $this->slugger = $slugger;
    }


    /**
     * @Route("/admin/product", name="admin_product")
     */
    public function product()
    {
        $products = $this->productRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/admin/product/create", name="admin_product_create")
     */
    public function create(Request $request) 
    {
        $product = new Product();
        $productAttr = new ProductAttr();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // On récupère le contenu de l'image passée dans le formulaire
            $picture = $form->get('picture')->getData();

            if ($picture) {
                // On crée le nom du fichier pour éviter doublon
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                // On crée un slug associé à l'$originalFilename
                $safeFilename = $this->slugger->slug($originalFilename);
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

            $product->setSlug(strtolower($this->slugger->slug($product->getName())));
            // On va chercher l'id de l'article et la qty correspondante défini dans le formulaire
            // Pour ensuite l'envoyer dans la table 
            $numberArticle = $form->get('quantity')->getData();
            $productAttr->setQuantity($numberArticle);
            $productAttr->setProduct($product);
            
            // On va chercher la taille de l'article défini dans le formulaire
            // Pour ensuite l'envoyer dans la table 
            $sizeArticle = $form->get('size')->getData();
            $productAttr->setProductSize($sizeArticle);

            $this->em->persist($product);
            $this->em->persist($productAttr);
            $this->em->flush();

            $this->addFlash('success', "Le produit a bien été crée");

            return $this->redirectToRoute('admin_product');
        }
        

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
            'slugger' => $product->getSlug(),
            'product' => $product
        ]);
    }


    /**
     * @Route("/admin/product/update/{id}", name="admin_product_update", requirements={"id":"\d+"})
     */
    public function update($id, Request $request, ProductAttrRepository $productAttrRepository) 
    {
        $product = $this->productRepository->find($id);
        // dd($product);
        $productAttr = $productAttrRepository->findOneByProduct(['product' => $product->getId()]);
        $currentSize = $productAttr->getProductSize()->getSize();

        if(!$product){
            throw $this->createNotFoundException("Le produit n°$id n'existe pas");
        }

        $form = $this->createForm(ProductType::class, $product);
        $formProductAttr = $this->createForm(ProductAttrType::class, $productAttr);


        $form->handleRequest($request);
        $formProductAttr->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($formProductAttr->isSubmitted() && $formProductAttr->isValid()){

            // On récupère le contenu de l'image passée dans le formulaire
            $picture = $form->get('picture')->getData();

            if ($picture) {
                // On crée le nom du fichier pour éviter doublon
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                // On crée un slug associé à l'$originalFilename
                $safeFilename = $this->slugger->slug($originalFilename);
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

            $productAttr->setProductSize($formProductAttr->get('newSize')->getData());  
            $product->setSlug(strtolower($this->slugger->slug($product->getName())));
            
            $this->em->flush();

            $this->addFlash('success', "Le produit a bien été mis à jour");

            return $this->redirectToRoute('admin_product');
            }
        }

        return $this->render('admin/update.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'formProductAttr' => $formProductAttr->createView(),
            'currentSize' => $currentSize,
        ]);
    }


    /**
     * @Route("/admin/product/delete/{id}", name="admin_product_delete")
     */
    public function delete(Product $product)
    {
        $this->em->remove($product);
        $this->em->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé');

        return $this->redirectToRoute('admin_product');
    }


    /**
     * @Route("/admin/product/activer/{id}", name="admin_active_product")
     */
    public function active(Product $product, $id): Response
    {
        $product->setActive(!$product->isActive());

        $this->em->persist($product);
        $this->em->flush();

        return $this->redirectToRoute('admin_product');
    }
}
