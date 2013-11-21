<?php
namespace Eventio\CronBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Ville Mattila <ville@eventio.fi>
 */
class CronEvent extends Event
{

    /**
     * An identifier for the current cron run
     *
     * @var string 
     */
    protected $runId;
    
    /**
     * The time when the cron run was originally triggered
     *
     * @var \DateTime
     */
    protected $cronTime;

    /**
     * @param $cronTime \DateTime The timestamp when the cron run was triggered
     * @param $runId    string    Optional identifier for the cron run
     */
    public function __construct(\DateTime $cronTime, $runId = null)
    {
        $this->cronTime = $cronTime;
        
        if (null !== $runId) {
            $this->runId = $runId;
        } else {
            $this->runId = $cronTime->format('Ymd-Hi');
        }
    }

    public function getRunId()
    {
        return $this->runId;
    }

    /**
     * @return \DateTime
     */
    public function getCronTime()
    {
        return $this->cronTime;
    }

}