<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(
                    Article $article,
                    ArticleRepository $repository)
    {

        return $this->render('article/index.html.twig', [
            'article' => $article,
        ]);
    }
}
