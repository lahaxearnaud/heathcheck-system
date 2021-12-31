<?php

namespace Alahaxe\HealthCheckBundle\Checks\Doctrine;

use Alahaxe\HealthCheck\Contracts\CheckInterface;
use Alahaxe\HealthCheck\Contracts\CheckStatusInterface;
use Alahaxe\HealthCheckBundle\CheckStatus;

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

        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $nbCore = count($matches[0]);
        } else {
            if ($loads === false) {
                return new CheckStatus(
                    $attributeName,
                    __CLASS__,
                    CheckStatus::STATUS_WARNING,
                    'Fail to check the number of cpu cors'
                );
            }
        }

        // load on 15 mins
        $load = $loads[2] / $nbCore;

        $status = CheckStatus::STATUS_OK;
        if ($freePercentage > $this->incidentLevelPercentageByCore) {
            $status = CheckStatus::STATUS_INCIDENT;
        } elseif ($freePercentage > $this->warningLevelPercentageByCore) {
            $status = CheckStatus::STATUS_WARNING;
        }

        return new CheckStatus(
            $attributeName,
            __CLASS__,
            $status
        );
    }
}
