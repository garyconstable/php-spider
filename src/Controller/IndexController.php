<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends AbstractController
{
    public $logger;

    /**
     * IndexController constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = new $logger('channel-name');
    }

    /**
     * ==
     * @param array $data
     * @param bool $die
     */
    public function d($data = [], $die = true)
    {
        echo '<pre>'.print_r($data, true).'</pre>';
        if ($die) {
            die();
        }
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        //$domains    = $em->getRepository('App:Domains')->tableSize();
        $ext        = $em->getRepository('App:ExternalDomain')->tableSize();
        //$queue      = $em->getRepository('App:Queue')->tableSize();
        //$pending    = $em->getRepository('App:Pending')->tableSize();
        $workers    = $em->getRepository('App:Process')->tableSize();

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'data' => [
                'domains'   => $ext[0]['total'],
                //'queue'     => $queue[0]['total'],
                //'pending'   => $pending[0]['total'],
                'workers'   => $workers[0]['total'],
            ]
        ]);
    }
}
