<?php 

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    
    /**
     * @Route("/contact", name="contact_index")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $contact = new Contact();

        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        
        if($formContact->isSubmitted() && $formContact->isValid()){
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('home_index');
        }

        return $this->render('contact/index.html.twig', [
            'formContact' => $formContact->createView(),
        ]);
    }

}