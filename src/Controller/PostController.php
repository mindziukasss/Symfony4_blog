<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/posts", name="posts")
 */


class PostController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(Post::class);

        $posts = $repository->findAll();

        return $this->render('post/index.html.twig',[
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();

        /**
         * @var Form
         */

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var UploadedFile $file
             */
            $file = $post->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessClientExtension();
            $file->move(
              $this->getParameter('images_directory'),
              $fileName
            );
            $post->setImage($fileName);
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl('postindex'));
        }
        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/{id}")
     * @param Post $post
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param SessionInterface $sessio
     */

    public function showAction(Post $post, SessionInterface $session)
    {

        $session->set('id', $post->getId());

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

}
