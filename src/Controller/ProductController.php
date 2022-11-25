<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger) 
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            // MODIFIER LA REDIRECTION VERS PAGE PRODUIT
            return $this->redirectToRoute('home_index');
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
    public function update($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em,
                            SluggerInterface $slugger) 
    {
        $product = $productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit n°$id n'existe pas");
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();

            // MODIFIER LA REDIRECTION VERS PAGE PRODUIT
            return $this->redirectToRoute('home_index');
        }

        return $this->render('product/update.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
