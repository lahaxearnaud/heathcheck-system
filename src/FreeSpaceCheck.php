<?php

namespace Alahaxe\HealthCheckBundle\Checks\Doctrine;

use Alahaxe\HealthCheck\Contracts\CheckInterface;
use Alahaxe\HealthCheck\Contracts\CheckStatusInterface;
use Alahaxe\HealthCheckBundle\CheckStatus;

class FreeSpaceCheck implements CheckInterface
{
    public function __construct(
        protected string $partition = '/',
        protected string $partitionName = 'freeSpace',
        protected float $warningLevelPercentage = .2,
        protected float $incidentLevelPercentage = .1,
    ) {
    }

    public function check(): CheckStatusInterface
    {
        $freePercentage = disk_free_space($this->partition) / disk_total_space($this->partition);

        $status = CheckStatus::STATUS_OK;
        if ($freePercentage < $this->incidentLevelPercentage) {
            $status = CheckStatus::STATUS_INCIDENT;
        } else if ($freePercentage < $this->warningLevelPercentage) {
            $status = CheckStatus::STATUS_WARNING;
        }

        return new CheckStatus(
            $this->partitionName,
            __CLASS__,
            $status
        );
    }
}
