<?php
namespace Eventio\CronBundle;

use Eventio\CronBundle\Event\CronEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract base class for implementing own CronHandler's
 *
 * @author Ville Mattila <ville@eventio.fi>
 */
abstract class CronHandler extends ContainerAware
{
    abstract public function run(CronEvent $cronEvent);
    
    // TODO: Helper function to check whether a given $cronTime matches
    // certain \DateInterval or standard cron definition
}
