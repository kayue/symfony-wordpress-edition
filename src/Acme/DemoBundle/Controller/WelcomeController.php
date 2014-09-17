<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');

        return $this->render('AcmeDemoBundle:Welcome:index.html.twig', [
            'posts' => $repo->findBy(['type' => 'post'])
        ]);
    }
}
