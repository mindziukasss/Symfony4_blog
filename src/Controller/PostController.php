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
 * @Route("/post", name="post")
 */


class PostController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->render('post/index.html.twig');
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

}
