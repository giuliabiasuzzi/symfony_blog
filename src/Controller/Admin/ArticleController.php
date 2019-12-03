<?php


namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller\Admin
 *
 * @Route("/article")
 */
class ArticleController extends AbstractController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/")
     */
    public function index(ArticleRepository $repository)
    {
        //lister les articles par date de publication décroissante
        // ds un tableau HTML
        // Afficher toutes les infos sauf le contenu

        $articles = $repository->findBy([], ['publicationDate' => 'DESC']);

        return $this->render(
            'admin/article/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }

    /**
     * @Route("/edition/{id}", defaults={"id":null}, requirements={"id": "\d+"})
     */
    public function edit(
        Request $request,
        EntityManagerInterface $manager,
        $id
    )
    {
        /*
         * ajouter la meme methode edit() qui fait le rendu du form de création/modification d'article
         *
         * Validation : tous les champs obligatoires
         *
         * En creation :
         * - setter l'auteur avec l'utilisateur connecté ($this->getUser() ds un controleur)
         * - setter la date de publication à maintenant
         *
         * Si le form est bien rempli, enregistrer en bdd
         * puis rediriger vers la liste avec un message de conf
         *
         * Mettre les liens ajouter et modifier ds la liste
         */
        if (is_null($id)) {
            $article = new Article();
        } else {
            $article = $manager->find(Article::class, $id);

            if (is_null($article)) {
                throw new NotFoundHttpException();
            }
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        //dump($article);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $article
                    //->setPublicationDate(new \DateTime()) // géréee ds le constructor
                    ->setAuthor($this->getUser());

                $manager->persist($article);
                $manager->flush();


                //message de conf
                $this->addFlash('success', 'l\'article est enregistré');

                //redirection vers la page de la liste
                return $this->redirectToRoute('app_admin_article_index');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render(
            'admin/article/edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/supression/{id}", requirements={"id": "\d+"})
     */
    public function delete(
        EntityManagerInterface$manager,
        Article $article
    ) {
        //suppression de l'article en bdd
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', 'L\'article est supprimé');
        return $this->redirectToRoute('app_admin_article_index');
    }


}