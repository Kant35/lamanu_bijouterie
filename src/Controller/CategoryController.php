<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="app_category")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/category/add", name="app_category_add")
     */
    public function addCategory(Request $request, CategoryRepository $categoryRepository)
    {
        // dump($request->request->all());
        // Je crée un nouvel objet de la classe Category().
        $category = new Category();

        // Je crée un formulaire
        $form = $this->createForm(CategoryType::class, $category);

        // Ici j'hydrate l'objet $category grâce aux informations reçu dans l'objet $request. 
        $form->handleRequest($request);

        // Je rentre dans la condition uniquement si mon formulaire est soumit et validé. 
        // La validation fait référence aux Contraintes que l'on place sur notre entité.
        if ($form->isSubmitted() && $form->isValid()) {
            // Je peux récupérer les données du formulaire grâce à la méthode getData()
            // dd($form->getData());

            // Ici j'utilise la méthode add() qui vient de ma classe CategoryRepository. 
            // Cette méthode agit avec l'ORM pour envoyer les données en BDD.
            $categoryRepository->add($category);
            
            // Ajout d'un message Flash qui sera envoyé à la vue.
            $this->addFlash("success", "La catégorie à été ajoutée");

            return $this->redirectToRoute("app_category");
        }

        // dump($category);


        return $this->render('category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="app_category_delete")
     */
    public function deleteCategory(Category $category, CategoryRepository $categoryRepository)
    {
        // Grâce au repository et à l'id récupéré dans l'URL on peut du coup faire une requêt en BDD pour récupérer un objet catégorie.
        // $category = $categoryRepository->find($id);
        
        // Ici je stock le nom de la catégorie avant de la supprimer pour pouvoir l'afficher dans mon message flash.
        $nomCategory = $category->getNom();
        
        // Ici on supprime la catégorie grâce au à la méthode remove() du repository.
        $categoryRepository->remove($category);

        $this->addFlash("danger", "La catégorie '$nomCategory' a été supprimée");

        return $this->redirectToRoute("app_category");
    }

    /**
     * @Route("/category/update/{id}", name="app_category_update")
     */
    public function updateCategory(Category $category, Request $request, EntityManagerInterface $manager)
    {
        // dump($category);

        $form = $this->createForm(CategoryType::class, $category);
        dump($category);

        $form->handleRequest($request);
        dump($category);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // la méthode persist() envoie l'objet à l'ORM sans pour autant l'envoyer en BDD. 
            // L'ORM va attendre notre 'go' pour envoyer l'objet. 
            // Cela permet du coup de pouvoir persister plusieurs objets. Une fois que j'ai fini mes persists, je pourrais tout envoyer en même temps dans la BDD.   
            $manager->persist($category);
            
            // le flush permet d'envoyer les objets persisté vers la BDD.
            $manager->flush();

            $this->addFlash('success', "La catégorie '" . $category->getNom() . "' à été modifiée");

            return $this->redirectToRoute("app_category");
        }

        return $this->renderForm('category/update.html.twig', [
            'form' => $form
        ]);
    }
}
