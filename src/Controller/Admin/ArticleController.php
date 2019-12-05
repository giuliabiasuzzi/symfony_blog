<?php


namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(
        ArticleRepository $repository,
        Request $request)
    {
        //lister les articles par date de publication décroissante
        // ds un tableau HTML
        // Afficher toutes les infos sauf le contenu

        /*
         * Ajouter une colonne avec le nombre de commentaires
         * qui soit un lien clicable vers une page qui liste les commentaires
         * de l'article avec la possibilité de les supprimer
         */

        // $articles = $repository->findBy([], ['publicationDate' => 'DESC']);
        // ligne remplacé par la requete sur le resultat du formulaire

        // formulaire de recherche ( pas de Entité liée )
        $searchForm = $this->createForm(SearchArticleType::class);
        $searchForm->handleRequest($request);

        // données du formulaire
        // dump($searchForm->getData());

        //creation de methode repository pour passer ce qui est recu par le formulaire
        // (array) est pour forcer le typage
        // > ca crée une tableau vide quand la valeure est null
        $articles = $repository->search((array)$searchForm->getData());

        return $this->render(
            'admin/article/index.html.twig',
            [
                'articles' => $articles,
                'search_form' => $searchForm->createView()
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

        $originalImage = null; //je definis l'image null par défaurl

        if (is_null($id)) { //creation
            $article = new Article();
        } else { //modification
            $article = $manager->find(Article::class, $id);

            if (is_null($article)) {
                throw new NotFoundHttpException();
            }
            //on verifie si l'article à modifier a un img
            if (!is_null($article->getImage())) {
                // on met le nom du ficher venant de la bdd
                // ds la  variable
                $originalImage = $article->getImage();
                // on sette l'image avec un nouveau obj File sur l'emplacement de l'img
                // pour le traitement par le formulaire
                $article->setImage(
                    new File($this->getParameter('upload_dir') . $originalImage));

            }
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        //dump($article);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                /**
                 * @var UploadedFile $image
                 */
                $image = $article->getImage();
                //s'il y a un image uploadée >> je lance son traitement
                if (!is_null($image)) {
                    // nom sous lequel on va enregistrer l'image
                    // guessExtension() > trouve extension à partir d'un file
                    $filename = uniqid() . '.' . $image->guessExtension();

                    // deplacement image uploadé du emplacement temp à son emplacement definitif
                    $image->move(
                    // repertoire vers lequel on va deplacer l'image (public/images)
                    //cf config/services.yaml
                        $this->getParameter('upload_dir'),
                        // nom unique du ficher
                        $filename
                    );
                    //set de l'attribut image de l'article avec le nom du ficher unique
                    $article->setImage($filename);

                    // en modification, on supprime l'ancienne photo
                    // s'il y en a une
                    if (!is_null($originalImage)) {
                        unlink($this->getParameter('upload_dir') . $originalImage);
                    }

                } else {
                    // en modification, sans upload, on sette l'image
                    // avec le nom de l'ancienne image
                    $article->setImage($originalImage);
                }

                $article
                    //->setPublicationDate(new \DateTime()) // géréee ds le constructor
                    ->setAuthor($this->getUser());

                $manager->persist($article);
                $manager->flush();


                // message de conf
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
                'form' => $form->createView(),
                'original_image' => $originalImage
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
        EntityManagerInterface $manager,
        Article $article
    )
    {

        // en suppresion, on supprime l'ancienne photo
        // s'il y en a une
        if (!is_null($article->getImage())) {

            $file = $this->getParameter('upload_dir') . $article->getImage();
            if (file_exists($file)) {
                unlink($file);
            }
        }

        //suppression de l'article en bdd
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', 'L\'article est supprimé');
        return $this->redirectToRoute('app_admin_article_index');
    }

    /**
     * @Route("/ajax/contenu/{id}")
     */
    public function ajaxContent(Article $article)
    {



        //on renvoie le contenu de l'article avec les saut de ligne
        return new Response(nl2br($article->getContent()));
    }

}