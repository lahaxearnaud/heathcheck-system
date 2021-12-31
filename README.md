# HealthCheck system checker

## Installation

Install checker:

```bash
    composer require alahaxe/healthcheck-system
```

Register service in your app:

```yaml
    Alahaxe\HealthCheckBundle\Checks\Doctrine\FreeSpaceCheck:
        # optional arguments
        arguments:
            $partition: "/"
            # must be different for each Alahaxe\HealthCheckBundle\Checks\Doctrine\FreeSpaceCheck instance
            $partitionName: "root-free-space"
            $warningLevelPercentage: 0.2
            $incidentLevelPercentage: 0.1
        tags: ['lahaxearnaud.healthcheck.check']

    Alahaxe\HealthCheckBundle\Checks\Doctrine\LoadCheck:
        # optional arguments
        arguments:
            $warningLevelPercentageByCore: 0.7
            $incidentLevelPercentageByCore: 0.9
        tags: ['lahaxearnaud.healthcheck.check']
```
