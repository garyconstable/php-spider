<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\DomainService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DomainsRepository;


class DomainListController extends AbstractController
{
    private $ds;
    private $dr;

    /**
     * IndexController constructor.
     * ==
     * @param DomainService $ds
     */
    public function __construct(LoggerInterface $logger, DomainService $ds, DomainsRepository $dr)
    {
        $this->ds = $ds;
        $this->dr = $dr;
    }

    /**
     * @Route("/domain/list", name="domain_list")
     */
    public function index()
    {
        return $this->render('domain_list/index.html.twig', [
            'controller_name' => 'DomainListController',
        ]);
    }

    /**
     * @Route("/api/domain/list")
     */
    public function domainList(Request $request)
    {
        $page = $request->query->get('page');

        if (!$page) {
            $page = 0;
        } else {
            $page--;
        }

        $limit = 10;

        $offset = $page * $limit;

        $total_domains = $this->ds->getDomainCount();

        $total_pages = ceil($total_domains / $limit);

        $results = [];

        $tmp = $this->dr->findBy([], ['id' => 'asc'], $limit, $offset);

        foreach ($tmp as $item) {
            $url = $item->getDomain();

            if (!preg_match('/http/', $url)) {
                $url = 'http://' . $url;
            }

            $results[] = [
                'id' => $item->getId(),
                'url' =>$url
            ];
        }

        return new JsonResponse([
            'success' => 'ok',
            'page' => $page,
            'total_pages' => $total_pages,
            'total_domains' => $total_domains,
            'data' => $results
        ]);
    }
}
