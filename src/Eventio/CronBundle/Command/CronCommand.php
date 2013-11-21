<?php
namespace Eventio\CronBundle\Command;

use Eventio\CronBundle\Event\CronEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Ville Mattila <ville@eventio.fi>
 */
class CronCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('eventio:cron')
            ->addOption('time', null, InputOption::VALUE_REQUIRED, 'Which minute cron should we start?')
            ->addOption('show-lock', null, InputOption::VALUE_NONE, 'If we do not get a lock, output details about the existing lock')
            ->addOption('force-lock', null, InputOption::VALUE_NONE, 'Force lock for our process.')
            ->addOption('do-not-run', null, InputOption::VALUE_NONE, 'Release a lock right away and do not fire the actual cron process. Can be combined with show-lock and force-lock.')
            ->setDescription('Starts a cron process')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('time')) {
            $cronTime = new \DateTime($input->getOption('time'), new \DateTimeZone('Etc/UTC'));
        } else {
            $cronTime = new \DateTime();
        }

        // Removing seconds from the timestamp
        $cronTime = new \DateTime($cronTime->format('Y-m-d H:i:00'));
        
        // Creating the run id from the defined time
        $runId = $cronTime->format('Ymd-Hi');

        // Acquiring lock in Redis
        // TODO: Move the locking logic to a separate component
        $redis = $this->getContainer()->get('snc_redis.default');
        $lockKey = 'eventio:cron:lock:' . $runId;
        $lockData = json_encode(array('pid' => getmypid(), 'host' => php_uname('n'), 'ts' => time()));
        $gotLock = $redis->setnx($lockKey, $lockData);
        if (!$gotLock) {
            if ($input->getOption('show-lock')) {
                $output->writeln('Stamp ' . $runId . ' was already locked: ' . $redis->get($lockKey));
            }
            if ($input->getOption('force-lock')) {
                $output->writeln('Forcing it to our process.');
                $redis->set($lockKey, $lockData);
            } else {
                return;
            }
        }

        // We'll expire the lock in 5 minutes.
        $redis->expire($lockKey, 60 * 5);

        if ($input->getOption('do-not-run')) {
            $redis->del($lockKey);
            $output->writeln('Lock released, will not run.');
            return;
        }

        $output->writeln('Starting cron process for time ' . $cronTime->format('c') . ', runId: ' . $runId);
        
        $this->getContainer()->get('event_dispatcher')
            ->dispatch('cron.tick', new CronEvent($cronTime, $runId));
        
        $output->writeln('Cron process completed.');
    }

}
