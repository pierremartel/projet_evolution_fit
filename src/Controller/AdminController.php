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
    /**
     * @Route("/admin/product", name="admin_product")
     */
    public function product(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'products' => $products,
        ]);
    }


    /**
     * @Route("/admin/product/create", name="admin_product_create")
     */
    public function create(EntityManagerInterface $em, SluggerInterface $slugger, Request $request) 
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
            // On va chercher l'id de l'article et la qty correspondante défini dans le formulaire
            // Pour ensuite l'envoyer dans la table 
            $numberArticle = $form->get('quantity')->getData();
            $productAttr->setQuantity($numberArticle);
            $productAttr->setProduct($product);
            
            // On va chercher la taille de l'article défini dans le formulaire
            // Pour ensuite l'envoyer dans la table 
            $sizeArticle = $form->get('size')->getData();
            $productAttr->setProductSize($sizeArticle);
            
            // dd($productAttr);

            $em->persist($product);
            $em->persist($productAttr);
            $em->flush();

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
    public function update($id, EntityManagerInterface $em, Request $request,
                            SluggerInterface $slugger, ProductRepository $productRepository,
                            ProductAttrRepository $productAttrRepository) 
    {
        $product = $productRepository->find($id);
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

            $productAttr->setProductSize($formProductAttr->get('newSize')->getData());  
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            
            $em->flush();

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
    public function delete(Product $product,
                            EntityManagerInterface $em)
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé');

        return $this->redirectToRoute('admin_product');
    }
}
