<?php

namespace Acme\DemoBundle\Controller;

use Kayue\WordpressBundle\Wordpress\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        $repo = $this->getWordpress()->getManager(1)->getRepository('KayueWordpressBundle:Post');
        $repo = $this->getWordpress()->getManager(2)->getRepository('KayueWordpressBundle:Post');

        return $this->render('AcmeDemoBundle:Welcome:index.html.twig', [
            'posts' => $repo->findBy(['type' => 'post'])
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
