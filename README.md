exacaster_api
=============

Exacaster public API


1. The API key shall be provided by your Exacaster account manager. 
2. The logging end-point shall be provided by your Exacaster account manager. 
3. You can use either Javascript or PHP class to log events to Exacaster.

See php_example.php script for an example how to use the PHP class.
See javascript_example.html script for an example how to use the Javascript class.

The main flow of usage is this (PHP example): 

1) Initalize class with your API key: 
$exq = new ExacasterMetrics('API_KEY');

2) Set the logging URL 
$exq->setTrackingUrl('wwwtrack.exacaster.com/e.php');

3) Call the Identify event to uniquely identify the customer who is performing the event. All subsequent set calls 
will automatically include the customer ID. 

$exq->identify('ss@ll.lt');

4) Log the event called "Log in": 

$exq->record('Log in', array( 'someParam' => 'data goes here'));

If you want to first set a lot of events in different code scripts and log with one call, use the "set" method: 

$exq->set(array('one' => 1, 'two' => 2));

... much later in code: 

$exq->record('Event Name');
