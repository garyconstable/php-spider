<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Swift_Mailer;


class IndexController extends AbstractController
{
    public $mailer;

    function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
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

    /**
     * @Route("/emailtest", name="email_test")
     */
    public function emailTest()
    {

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('garyconstable80@gmail.com')
            ->setTo('garyconstable80@gmail.com')
            ->setBody(
            'Hello this is a test',
            'text/html'
            )
            ;

        try{
            $this->mailer->send($message);
        }catch(\Exception $ex){
            echo $ex->getMessage();
            die();
        }

        return new JsonResponse('hello');

    }
}
