<?php

require 'src/exq.php';

$exq = new ExacasterMetrics('API_KEY');
// Possible agents: fsock, test, wget
$exq->useTransferAgent('test');

$exq->identify('ss@ll.lt');

$exq->record('Log in', array( 'someParam' => 'one-two-trečias-brolis-ąčęėįšųž'));
$exq->record('second-event');

$exq->set(array('one' => 1, 'two' => 2));

$exq->record('multi-params');
$exq->record('multi-params-again');

$exq->alias('ss', 'ss@ll.lt');

$exq->setTrackingUrl('wwwtrack.exacaster.com/log');
$exq->record('multi-params, but different URL');
?>
