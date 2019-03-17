<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/spider-status", name="spider_status")
     */
    public function spider_status()
    {
        $em = $this->getDoctrine()->getManager();
        $domains    = $em->getRepository('App:Domains')->tableSize();
        $queue      = $em->getRepository('App:Queue')->tableSize();
        $pending    = $em->getRepository('App:Pending')->tableSize();

        return new JsonResponse(array(
            'domains'   => $domains[0]['total'],
            'queue'     => $queue[0]['total'],
            'pending'   => $pending[0]['total']
        ));
    }
}
