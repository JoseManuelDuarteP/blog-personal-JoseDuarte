<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Comentario;
use App\Form\ComentarioFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Image;

final class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repoImages = $doctrine->getRepository(Image::class);
        $images = $repoImages->findAll();

        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
            'images' => $images,
        ]);
    }

    #[Route('/blog', name: 'blog')]
    public function blog(): Response
    {
        return $this->render('page/blog.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, ManagerRegistry $doctrine): Response
    {
        $comentario = new Comentario();
        $form = $this->createForm(ComentarioFormType::class, $comentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($comentario);
            $entityManager->flush();
            
            // $session = new Session();
            // $flashes = $session->getFlashBag();

            // $flashes->add('success', 'Comentario enviado con éxito.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('page/contact.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'PageController',
        ]);
    }
}
