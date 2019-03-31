<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinkSpiderController extends AbstractController
{
    /**
     * @Route("/link/spider", name="link_spider")
     */
    public function index()
    {
        return $this->render('link_spider/index.html.twig', [
            'controller_name' => 'LinkSpiderController',
        ]);
    }

    /**
     * ==
     * @param array $data
     * @param bool $die
     */
    public function d($data = [], $die = TRUE)
    {
        echo '<pre>'.print_r($data, TRUE).'</pre>';
        if($die){
            die();
        }
    }

    /**
     * @Route("/link/spider/status", name="link_spider_status")
     */
    public function spider_status()
    {
        $em = $this->getDoctrine()->getManager();
        $domains    = $em->getRepository('App:Domains')->tableSize();
        $queue      = $em->getRepository('App:Queue')->tableSize();
        $pending    = $em->getRepository('App:Pending')->tableSize();
        $tmp        = $em->getRepository('App:Process')->findBy(['worker_type' => 'spider_worker']);
        $workers    = [];

        foreach($tmp as $worker){
            $workers[] = [
                'id'            => $worker->getId(),
                'pid'           => $worker->getPid(),
                'worker_type'   => $worker->getworkerType(),
                'date'          => $worker->getDateAdd()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse(array(
            'domains'   => $domains[0]['total'],
            'queue'     => $queue[0]['total'],
            'pending'   => $pending[0]['total'],
            'workers'   => $workers
        ));
    }

    /**
     * @Route("/link/spider/start", name="link_spider_start")
     */
    public function spider_start()
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:worker > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);
        return new JsonResponse(array(
            'message' => 'done',
            'pid'     => $pid
        ));
    }

    /**
     * @Route("/link/spider/stop", name="link_spider_stop")
     */
    public function spider_stop()
    {
        $pid = false;
        if(isset($_GET['pid'])){
            $pid = $_GET['pid'];
        }
        if($pid){
            $dir =  rtrim(dirname(__DIR__, 2), '/') ;
            $command = "php " . $dir . "/bin/console spider:worker:stop_pid ".$pid." > /dev/null 2>&1 & echo $!;";
            $pid = exec($command, $output);
            return new JsonResponse(array(
                'message' => 'done',
                'pid'     => $pid
            ));
        }
        return new JsonResponse(array(
            'message' => 'done'
        ));
    }
}
