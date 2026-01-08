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
use App\Repository\ImageRepository;
use App\Repository\LikeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Like;

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

    #[Route('/imagenes/{categoria}', name: 'imagenes_por_categoria')]
    public function imagenesPorCategoria(string $categoria, ImageRepository $imageRepository): JsonResponse 
        {

        if ($categoria === 'All') {
            $imagenes = $imageRepository->findAll();
        } else {
            $imagenes = $imageRepository->findByCategoryName($categoria);
        }

        $data = [];

        foreach ($imagenes as $imagen) {
            $data[] = [
                'title' => $imagen->getTitle(),
                'file' => $imagen->getFile(),
                'category' => $imagen->getCategory()->getName(),
                'views' => $imagen->getNumViews(),
                'likes' => $imagen->getNumLikes(),
                'price' => $imagen->getPrice(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/product_file/{id}', name: 'product_file')]
    public function productFile($id, ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Image::class);
        $image = $repo->find($id);

        return $this->render('page/product_file.html.twig', [
            'controller_name' => 'PageController',
            'image' => $image,
        ]);
    }
        //Funciona, hacerlo con ajax para no recargar la pagina
    #[Route('/like/{imageId}', name: 'like_image')]
    public function likeImage(int $imageId,
    ImageRepository $imageRepository, 
    LikeRepository $likeRepository, 
    ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $image = $imageRepository->find($imageId);

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$image) {
            return $this->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        if ($likeRepository->hasUserLikedImage($user->getId(), $imageId)) {
            $likeDelete = $doctrine->getRepository(Like::class)->findOneBy([
                'user' => $user,
                'image' => $image
            ]);
            $image->setNumLikes($image->getNumLikes() - 1);
            $em = $doctrine->getManager();
            $em->persist($image);
            $em->remove($likeDelete);
            $em->flush();
            //return $this->json(['message' => 'You have already liked this image'], Response::HTTP_BAD_REQUEST);
            return $this->render('page/product_file.html.twig', [
                'controller_name' => 'PageController',
                'image' => $image,
            ]);
        }

        $like = new Like($user, $image);
        $image->setNumLikes($image->getNumLikes() + 1);
        $em = $doctrine->getManager();
        $em->persist($like);
        $em->persist($image);
        $em->flush();
        //return $this->json(['message' => 'Post liked successfully'], Response::HTTP_OK);
        return $this->render('page/product_file.html.twig', [
            'controller_name' => 'PageController',
            'image' => $image,
        ]);
    }
}