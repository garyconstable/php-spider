<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService;
use Psr\Log\LoggerInterface;

class DomainSpiderController extends AbstractController
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
     * @Route("/domain/spider", name="domain_spider")
     */
    public function index()
    {
        return $this->render('domain_spider/index.html.twig', [
            'controller_name' => 'DomainSpiderController',
        ]);
    }

    /**
     * @Route("/domain/spider/status", name="domain_spider_status")
     */
    public function spiderStatus()
    {
        $em = $this->getDoctrine()->getManager();
        $domains    = $this->ds->getDomainCount();
        $queue      = $em->getRepository('App:Queue')->tableSize();
        $pending    = $em->getRepository('App:Pending')->tableSize();
        $tmp        = $em->getRepository('App:Process')->findBy(['worker_type' => 'domain_worker']);
        $workers    = [];

        foreach ($tmp as $worker) {
            $workers[] = [
                'id'            => $worker->getId(),
                'pid'           => $worker->getPid(),
                'worker_type'   => $worker->getworkerType(),
                'worker_url'    => $worker->getworkerUrl(),
                'date'          => $worker->getDateAdd()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse(array(
            'domains'   => $domains,
            'queue'     => $queue[0]['total'],
            'pending'   => $pending[0]['total'],
            'workers'   => $workers
        ));
    }

    /**
     * @Route("/domain/spider/start", name="domain_spider_start")
     */
    public function spiderStart()
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:domain:crawl > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);
        return new JsonResponse(array(
            'message' => 'done',
            'pid'     => $pid
        ));
    }
}
