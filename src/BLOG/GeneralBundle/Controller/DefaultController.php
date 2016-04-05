<?php

namespace BLOG\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BLOGGeneralBundle:Default:index.html.twig', array('name' => $name));
    }
}
