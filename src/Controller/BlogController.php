<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class BlogController extends AbstractController
{
    #[Route('/single_post/{slug?}', name: 'single_post')]
    public function post(ManagerRegistry $doctrine, $slug = null): Response
    {
        $repositorio = $doctrine->getRepository(Post::class);
        $post = $slug ? $repositorio->findOneBy(["slug"=>$slug]) : null;
        $recents = $repositorio->findRecents();
        return $this->render('blog/single_post.html.twig', [
            'post' => $post,
            'recents' => $recents
        ]);
    }

    #[Route('/blog/new', name: 'new_post')]
    public function newPost(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        // Comprobar si el usuario ha iniciado sesión para reenviarlo a login
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            // Procesar la subida de la imagen
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('blog_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return new Response("Error al subir el archivo: " . $e->getMessage());
                }

                $post->setImage($newFilename);
            }

            // Quitamos los caracteres especiales del título para crear el slug
            $post->setSlug($slugger->slug($post->getTitle()));

            //Guardamos el usuario que crea el post y los contadores a 0
            $post->setPostUser($this->getUser());
            $post->setNumLikes(0);
            $post->setNumComments(0);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('single_post', ["slug" => $post->getSlug()]);
        }
        return $this->render('blog/new_post.html.twig', array(
                'form' => $form->createView()
        ));
    }

    #[Route('/blog/{page}', name: 'blog', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function index(ManagerRegistry $doctrine, int $page = 1): Response
    {
        $repositorio = $doctrine->getRepository(Post::class);
        $posts = $repositorio->findAll();
        $recents = $repositorio->findRecents();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'recents' => $recents
        ]);
    }

}
