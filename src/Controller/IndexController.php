<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
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
    public function index(
        ArticleRepository $articleRepository
    )
    {
        // les 5 dernieres articles de la categorie en ordre de date
        $articles = $articleRepository->findBy([], ['publicationDate' =>'DESC'], 5);
        return $this->render('index/index.html.twig',
            [
                'articles' => $articles
            ]);
    }
}
