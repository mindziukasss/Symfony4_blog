<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/", name="posts")
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="index",  methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine();
        $repository = $em->getRepository(Post::class);

        $posts = $repository->findAll();


        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        $user_id = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $post = new Post();

        /**
         * @var Form
         */

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file = $post->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            $post->setUserId($user_id);
            $post->setImage($fileName);
            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('postsindex'));
        }
        return $this->render('post/new.html.twig', [
            'form_new_post' => $form->createView()
        ]);

    }

    /**
     * @Route("posts/{id}", name="show", methods={"GET"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function showAction($id)
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $userId = $this->getUser()->getId();


        $em = $this->getDoctrine()->getManager();
        $post = $em
            ->getRepository(Post::class)
            ->find($id);

        if ($post->getUserId() != $userId) {
            return $this->redirect($this->generateUrl('postsindex'));
        }

        if (!$post) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file = $post->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );
            $post->setImage($fileName);
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute("postsindex");
        }

        return $this->render("post/edit.html.twig", [
            'form_edit_post' => $form->createView()
        ]);

    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $post = $em
            ->getRepository(Post::class)
            ->find($id);

        if ($post->getUserId() != $userId) {
            return $this->redirect($this->generateUrl('postsindex'));
        }

        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('postsindex');

    }


}
