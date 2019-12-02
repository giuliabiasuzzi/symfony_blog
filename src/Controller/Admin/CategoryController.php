<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 *
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{

    /**
     * Url de la page est admin/categorie (cf config/routes/annotation.yaml
     * nom de la route est app_admin_category_index
     *
     * @Route("/")
     */
    public function index(CategoryRepository $repository)
    {
        // $categories = $repository->findAll();
        // avel le findAll() les éléments vont pour ordre de "nom" par défaut
        // éq d'un findAll() avec un tri sur l' id
        $categories = $repository->findBy([], ['id' => 'ASC']);

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * {id} est optionnel,
     * vaut null par défaut et
     * doit être un nombre si renseigné
     *
     * @Route("/edition/{id}", defaults={"id":null}, requirements={"id": "\d+"})
     */
    public function edit(
        Request $request,
        EntityManagerInterface $manager,
        $id
    )
    {
        if (is_null($id)) {
            //si le parametre variable {id} est vide >> création
            //instantiation d'une category
            $category = new Category();
        } else { //modification
            //je demande au manager d'aller chercher la catègory
            // à laquelle corresponde le {id de la partie variable}
            $category = $manager->find(Category::class, $id);

            //404 si le id n'est pas en bdd
            if(is_null($category)){
                throw new NotFoundHttpException();
            }

        }
        // création d'un form, rélié à la catégorie
        // auquel on va passer le $category qu'on viet de créer avec la methode createForm()
        $form = $this->createForm(CategoryType::class, $category);

        /*
         * Le form analise la requete
         * et fait le mapping avec l'entité s'il a été soumis
         */
        $form->handleRequest($request);
        //dump($category);

        // si le form a été soumis
        if ($form->isSubmitted()) {
            // si les validations à partir des annotations
            // ds l'entité Category sont ok
            if ($form->isValid()) {
                //EntityManager enregistre la catégorie ds la bdd
                $manager->persist($category);;
                $manager->flush();

                //message de confirmation
                $this->addFlash('success', 'La catégorie est enregistrée');

                //redirection vers la page de liste
                return $this->redirectToRoute('app_admin_category_index');
            } else {
                // dump($form->getErrors(false, true)); // il faut faire en suite une iteration pour recuperer tous les messages un par un
                //message d'erreur
                $this->addFlash('error', 'Le formulare contient des erreurs');
            }
        }

        return $this->render(
            'admin/category/edit.html.twig',
            [
                // passage du form
                // on passe un obj de la class FormView() (= la view du form)
                'form' => $form->createView()
            ]
        );
    }

    /**
     * pas de défault null car le id est obligatoire pour la suppression
     * @Route("/supression/{id}", requirements={"id": "\d+"})
     */
    public function delete(
        EntityManagerInterface $manager,
        Category $category
    ) {
        //suppression de la catégorie en bdd
     $manager->remove($category);
     //remove comme prsist a besoin d'un flush
     $manager->flush();

     $this->addFlash('success', 'La catégorie est supprimée');
     return $this->redirectToRoute('app_admin_category_index');
    }
}