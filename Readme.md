Symfony2 Bundle for Cron Jobs in Distributed Environment
========================================================

**Work in progress** for Symfony2 Bundle for running Cron Jobs in Distributed Environment.

Current version requires also [SncRedisBundle](https://github.com/snc/SncRedisBundle)
to do global lock. Locking will be moved to a separated component and abstracted.

Usage
-----

* Create a listener for `cron.tick` event, an instance of `CronEvent` is passed as an argument
* Check `$event->getCronTime()` and decide whether you need to do at the given time

License
-------

Copyright [Eventio Oy](https://github.com/eventio), [Ville Mattila](https://github.com/vmattila), 2013

Released under the [The MIT License](http://www.opensource.org/licenses/mit-license.php)