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
    #[Route('/like/{imageId}', name: 'like_image', methods: ['POST'])] // Limitamos a POST
    public function likeImage(
        int $imageId,
        ImageRepository $imageRepository, 
        LikeRepository $likeRepository, 
        ManagerRegistry $doctrine
    ): JsonResponse // Cambiamos el tipo de retorno a JsonResponse
    {
        $user = $this->getUser();
        
        // Si no hay usuario, devolvemos un error 403 o 401 en formato JSON
        if(!$user) {
            return $this->json(['error' => 'Usuario no logueado'], Response::HTTP_UNAUTHORIZED);
        }

        $image = $imageRepository->find($imageId);

        if (!$image) {
            return $this->json(['message' => 'Imagen no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $em = $doctrine->getManager();
        $liked = false; // Variable para saber si acabó dando like o quitándolo

        if ($likeRepository->hasUserLikedImage($user->getId(), $imageId)) {
            // QUITAR LIKE
            $likeDelete = $doctrine->getRepository(Like::class)->findOneBy([
                'user' => $user,
                'image' => $image
            ]);
            
            $image->setNumLikes($image->getNumLikes() - 1);
            $em->remove($likeDelete);
            $liked = false;
        } else {
            // DAR LIKE
            $like = new Like($user, $image);
            $image->setNumLikes($image->getNumLikes() + 1);
            $em->persist($like);
            $liked = true;
        }

        $em->persist($image);
        $em->flush();

        // Devolvemos JSON con el nuevo número y el estado
        return $this->json([
            'numLikes' => $image->getNumLikes(),
            'liked' => $liked // Útil si quieres cambiar el color del botón
        ]);
    }
}