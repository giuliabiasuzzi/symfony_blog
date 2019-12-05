<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/{id}", defaults={"id":null}, requirements={"id": "\d+"})
     */
    public function index(
        Category $category,
        ArticleRepository $articleRepository)

    {
        /*
         * Lister les 2/3 derniers articles en date de la categorie
         * avec un lien vers une page article à créer ds un nouveau conrolleur Article
         * qui affiche le détail de l'article et son image, si presente
         */

        // les 3 dernieres articles de la categorie en ordre de date
        $articles = $articleRepository->findBy(
                                            ['category' => $category],
                                            ['publicationDate' =>'DESC'],
                                                3);
        //dump($articles);

        return $this->render(
            'category/index.html.twig',
            [
                'category' => $category,
                'articles' => $articles
            ]
        );
    }

    // pas de route > methode que va recuperer toutes les categories
    // pour les ingecter ds la nav
    public function menu(CategoryRepository $repository)
    {
        $categories = $repository->findBy([], ['name' => 'ASC']);

        return $this->render(
            'category/menu.html.twig',
            [
                'categories' => $categories
            ]
        );
    }
}
