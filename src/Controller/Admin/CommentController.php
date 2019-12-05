<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 * @package App\Controller\Admin
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/{id}", defaults={"id":null}, requirements={"id": "\d+"})
     */
    public function index(
        Article $article,
        CommentRepository $commentRepository
    )
    {
        $comments = $commentRepository->findBy(
            ['article' => $article],
            ['publicationDate' => 'ASC']
        );
        return $this->render('admin/comment/index.html.twig',
            [
                'article' => $article,
                'comments' => $comments
            ]);
    }

    /**
     * @Route("/supression/{id}", requirements={"id": "\d+"})
     */
    public function delete(
        EntityManagerInterface $manager,
        Comment $comment
    )
    {
        //suppression d'un commentaire en bdd
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('app_admin_comment_index',
            [
                'id' => $comment->getArticle()->getId()
            ]);
    }
}
