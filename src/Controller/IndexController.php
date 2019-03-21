<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends AbstractController
{
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
        $workers    = $em->getRepository('App:Process')->tableSize();

        return new JsonResponse(array(
            'domains'   => $domains[0]['total'],
            'queue'     => $queue[0]['total'],
            'pending'   => $pending[0]['total'],
            'workers'   => $workers[0]['total']
        ));
    }

    /**
     * @Route("/spider-start", name="spider_start")
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
     * @Route("/spider-stop", name="spider_stop")
     */
    public function spider_stop()
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:worker:stop > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);
        return new JsonResponse(array(
            'message' => 'done',
            'pid'     => $pid
        ));
    }
}
