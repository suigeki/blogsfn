<?php
// src/BLOG/GeneralBundle/Controller/CategoryController.php

namespace BLOG\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Classe de l'entité Category
use BLOG\GeneralBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    public function listAction($limit=null)
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère toutes les catégories
        $listCategories = $em->getRepository('BLOGGeneralBundle:Category')->findBy(
                                        array(), // array('author' => 'Alexandre') Critere
  					array('name' => 'asc'),        // Tri
  					$limit,                        // Limite
  					0                              // Offset        
                                                        );
        
        //return new Response('Index du controller Category !');
        return $this->render('BLOGGeneralBundle:Category:list.html.twig', 
                                        array(
                                            'listCategories' => $listCategories
                                        ));
    }
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère toutes les catégories
        $listCategories = $em->getRepository('BLOGGeneralBundle:Category')->findAll
();
        //return new Response('Index du controller Category !');
        return $this->render('BLOGGeneralBundle:Category:index.html.twig', 
                                        array(
                                            'listCategories' => $listCategories
                                        ));
    }
    
    public function viewAction($id, $slug)
    {   
        $em = $this->getDoctrine()->getManager();
        // On récupère toutes les catégories
        $category = $em->getRepository('BLOGGeneralBundle:Category')->find($id);
        //return $this->render('BLOGGeneralBundle:Category:view.html.twig');
        return $this->render('BLOGGeneralBundle:Category:view.html.twig', 
                                        array(
                                            'category' => $category
                                        ));
    }
    
    public function addAction(Request $request)
    {   
        // On crée un objet Category pour le formulaire
        $category = new Category();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $category);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
                ->add('name', 'text', array('required' => true))
                ->add('active', 'checkbox', array('data' => true))
                ->add('save', 'submit')
        ;
        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();
        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $category contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
            // On l'enregistre notre objet $advert dans la base de données, par exemple
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien enregistrée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('blog_general_categoryview', array('id' => $category->getId(), 'slug' => $category->getName())));
        }
        
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('BLOGGeneralBundle:Category:add.html.twig', array(
                                        'form' => $form->createView(),
                                        'action' => 'Ajout',
                                ));
        //S'il s'agit d'une requête émise par le formulaire
        /*
        if ($request->isMethod('POST')) 
        {
            $request->getSession()->getFlashBag()->add('notice', 'Catégorie bien enregistrée.');
            return $this->redirectToRoute('blog_general_categoryview', array('id' => 5));
        }
        
        return $this->render('BLOGGeneralBundle:Category:add.html.twig');
        */
    }
    
    public function editAction($id, Request $request)
    {   
        // Récupération de la catégorie
        $category = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('BLOGGeneralBundle:Category')
                                ->find($id)
        ;
        
        // Et on construit le formBuilder avec cette instance de l'annonce, comme précédemment
        $formBuilder = $this->get('form.factory')->createBuilder('form', $category);
        
        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
                ->add('name', 'text', array('required' => true))
                ->add('active', 'checkbox')
                ->add('save', 'submit')
        ;
        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();
                // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $category contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
            // On l'enregistre notre objet $advert dans la base de données, par exemple
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'La catégorie a été modifiée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('blog_general_categoryview', array('id' => $category->getId(), 'slug' => $category->getName())));
        }
        
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('BLOGGeneralBundle:Category:edit.html.twig', array(
                                        'form' => $form->createView(),
                                        'action' => 'Modification'
                                ));
    }
    
    public function deleteAction($id)
    {   
        //On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $category = $em->getRepository('BLOGGeneralBundle:Category')->find($id);
        if (null === $category) {
            throw new NotFoundHttpException("La catégorie avec l'id ".$id." n'existe pas.");
        }
        $em->remove($category);
        $em->flush();
        return $this->redirect($this->generateUrl('BLOGGeneralBundle:Category:index.html.twig'));
    }
}