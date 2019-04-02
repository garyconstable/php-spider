<?php

namespace App\Service;
use App\Service\DomainWorkerService;



class DomainWorkerPoolService implements \Countable
{
    private $maxPoolSize = 0;
    private $occupiedWorkers = [];
    private $freeWorkers = [];

    /**
     *
     * DomainWorkerPoolService constructor.
     * ==
     * @param int $poolSize
     */
    public function __construct( $poolSize = 0 )
    {
        $this->maxPoolSize = $poolSize;
        for($i=0;$i<$poolSize;$i++){
            $this->freeWorkers[] = new DomainWorkerService();
        }
    }

    /**
     *
     * ==
     * @return \App\Service\DomainWorkerService
     */
    public function get()
    {
        if (count($this->freeWorkers) == 0 && count($this->occupiedWorkers) < $this->maxPoolSize ) {
            $worker = new DomainWorkerService();
        } else {
            $worker = array_pop($this->freeWorkers);
        }

        if(!is_null($worker)) {
            $this->occupiedWorkers[spl_object_hash($worker)] = $worker;
            return $worker;
        }

        return false;
    }

    /**
     *
     * ==
     * @param \App\Service\DomainWorkerService $worker
     */
    public function dispose(DomainWorkerService $worker)
    {
        $key = spl_object_hash($worker);
        if (isset($this->occupiedWorkers[$key])) {
            unset($this->occupiedWorkers[$key]);
            $this->freeWorkers[$key] = $worker;
        }
    }

    /**
     *
     * ==
     * @param string $key
     */
    public function disposeByKey($key = '')
    {
        if (isset($this->occupiedWorkers[$key])) {
            unset($this->occupiedWorkers[$key]);
            $this->freeWorkers[$key] = new DomainWorkerService();
        }
    }

    /**
     *
     * ==
     * @return int
     */
    public function count(): int
    {
        return count($this->occupiedWorkers) + count($this->freeWorkers);
    }

    /**
     *
     * ==
     * @param int $max
     */
    public function setMaxWorkers($max = 0)
    {
        $this->maxPoolSize = $max;
    }
}