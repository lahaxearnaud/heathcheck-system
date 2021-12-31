<?php

namespace Alahaxe\HealthCheckBundle\Checks\System;

use Alahaxe\HealthCheck\Contracts\CheckInterface;
use Alahaxe\HealthCheck\Contracts\CheckStatusInterface;
use Alahaxe\HealthCheck\Contracts\CheckStatus;

class LoadCheck implements CheckInterface
{
    public function __construct(
        protected float $warningLevelPercentageByCore = .7,
        protected float $incidentLevelPercentageByCore = .9,
    ) {
    }

    public function check(): CheckStatusInterface
    {
        $loads = sys_getloadavg();

        $attributeName = 'cpu-load';
        if ($loads === false) {
            return new CheckStatus(
                $attributeName,
                __CLASS__,
                CheckStatus::STATUS_WARNING,
                'Fail to check cpu load'
            );
        }

        $nbCore = $this->getNumberOfCPUs();
        if ($nbCore === 0) {
            return new CheckStatus(
                $attributeName,
                __CLASS__,
                CheckStatus::STATUS_WARNING,
                'Fail to check the number of cpu cors'
            );
        }
        // load on 5 mins
        $load = $loads[1] / $nbCore;

        $status = CheckStatus::STATUS_OK;
        if ($load > $this->incidentLevelPercentageByCore) {
            $status = CheckStatus::STATUS_INCIDENT;
        } elseif ($load > $this->warningLevelPercentageByCore) {
            $status = CheckStatus::STATUS_WARNING;
        }

        return new CheckStatus(
            $attributeName,
            __CLASS__,
            $status
        );
    }

    protected function getNumberOfCPUs() :int {
        if (PHP_OS_FAMILY == 'Windows') {
            $cores = shell_exec('echo %NUMBER_OF_PROCESSORS%');
        } else { // linux & macos
            $cores = shell_exec('nproc');
        }

        return (int) $cores;
    }
}
