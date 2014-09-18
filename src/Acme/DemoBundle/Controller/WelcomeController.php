<?php

namespace Acme\DemoBundle\Controller;

use Kayue\WordpressBundle\Wordpress\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('AcmeDemoBundle:Welcome:index.html.twig', [
            'blog1' => $this->getWordpress()->getManager()->getRepository('KayueWordpressBundle:Post')->findBy(['type' => 'post']),
            'blog2' => $this->getWordpress()->getManager(2)->getRepository('KayueWordpressBundle:Post')->findBy(['type' => 'post']),
            'blog3' => $this->getWordpress()->getManager(3)->getRepository('KayueWordpressBundle:Post')->findBy(['type' => 'post']),
        ]);
    }

    /**
     * @return ManagerRegistry
     */
    protected function getWordpress()
    {
        return $this->get('kayue_wordpress');
    }
}
