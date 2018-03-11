<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/post")
 */


class PostController extends Controller
{
    /**
     * @Route("/", name="post")
     */
    public function indexAction()
    {
        return $this->render('post/index.html.twig');
    }
}
