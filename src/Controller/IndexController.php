<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * page d'accueil
     *
     * @Route("/")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }
}
