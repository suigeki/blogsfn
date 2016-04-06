<?php
// src/BLOG/GeneralBundle/Controller/CategoryController.php

namespace BLOG\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Classe de l'entité Article
use BLOG\GeneralBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use BLOG\Form\DataTransformer\DateTimeTransformer;

class ArticleController extends Controller
{
    public function indexAction()
    {
        return new Response("Index du controller Article !");
    }
    
    public function listAction($limit=null)
    {
        $em = $this->getDoctrine()->getManager();
        // On récupère toutes les catégories
        $listArticles = $em->getRepository('BLOGGeneralBundle:Article')->findBy(
                                        array(), // array('author' => 'Alexandre') Critere
  					array('title' => 'asc'),        // Tri
  					$limit,                        // Limite
  					0                              // Offset        
                                                        );
        
        //return new Response('List du controller Article !');
        return $this->render('BLOGGeneralBundle:Article:list.html.twig', 
                                        array(
                                            'listArticles' => $listArticles
                                        ));
    }
    
    public function viewAction($id, $slug)
    {   
        $em = $this->getDoctrine()->getManager();
        // On récupère tous les articles
        $article = $em->getRepository('BLOGGeneralBundle:Article')->find($id);
        
        //return $this->render('BLOGGeneralBundle:Article:view.html.twig');
        return $this->render('BLOGGeneralBundle:Article:view.html.twig', 
                                        array(
                                            'article' => $article,
                                            'slug' => $slug
                                        ));
    }
    
    public function addAction(Request $request)
    {   
        // On crée un objet Article pour le formulaire
        $article = new Article();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder('form', $article);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
                /*->add('date', 'text', array(
                                            'required' => true,
                                            'label' => 'form.label.datetime',
                                            'translation_domain' => 'BLOG',
                                            'attr' => array(
                                                'class' => 'form-control input-inline datetimepicker',
                                                'data-provide' => 'datepicker',
                                                'data-format' => 'dd-mm-yyyy HH:ii',
                                            ),
                                        ))*/
                ->add('date',                    'date',array('required' => false,
                                                      'widget' =>'single_text',
                                                      'format' =>'dd-MM-yyyy'))
                ->add('title', 'text', array('required' => true))
                ->add('content', 'text', array('required' => true))
                ->add('active', 'checkbox', array('data' => true))
                ->add('save', 'submit')
                //->get('date')->addModelTransformer(new DateTimeTransformer());
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
            $em->persist($article);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirect($this->generateUrl('blog_general_articleview', array('id' => $article->getId(), 'slug' => $article->getTitle())));
        }
        
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('BLOGGeneralBundle:Article:add.html.twig', array(
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
}
