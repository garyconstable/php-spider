<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService;

class IndexController extends AbstractController
{
    public $logger;
    private $ds;

    /**
     * IndexController constructor.
     * ==
     * @param LoggerInterface $logger
     * @param DomainService $ds
     */
    public function __construct(LoggerInterface $logger, DomainService $ds)
    {
        $this->logger = new $logger('channel-name');
        $this->ds = $ds;
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
        $domains = $this->ds->getDomainCount();
        $workers = $em->getRepository('App:Process')->tableSize();

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'data' => [
                'domains'   => $domains,
                'workers'   => $workers[0]['total'],
            ]
        ]);
    }
}
