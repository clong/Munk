<?php
// Include the Maltego and Splunk classes
include_once("Maltego.php");
include_once("Splunk.php");
include_once("settings.php");

// Set return content-type to be XML
header ("content-type: text/xml");

$maltegoInput = new MaltegoTransformInput();
$maltegoTransform = new MaltegoTransformResponse();

// Get the details into local variables
$maltegoInput->getEntity();
$src_ip = $maltegoInput->value;
$result_field = 'dst_ip';
$entityFields = $maltegoInput->transformFields;
$start_date = $entityFields['StartDate'];
$start_date = DateTime::createFromFormat('Ymd',$start_date)->format('m/d/Y:00:00:00');
$end_date = $entityFields['EndDate'];
$end_date = DateTime::createFromFormat('Ymd', $end_date)->format('m/d/Y:00:00:00');

// Ensure we have an entity to work with
if (!$src_ip) {
  $maltegoTransform->addException("No input entity found");
  $maltegoTransform->throwExceptions();
}
 
try {
  // Splunk search query
  $search = 'search index=main src_ip='.$src_ip
    .' earliest='.$start_date
    .' latest='.$end_date
    .' | fields '.$result_field;
  // Login and start search job
  $service = new Splunk_Service($SplunkExamples_connectArguments);
  // (NOTE: Can throw HTTP 401 if bad credentials)
  $service->login();
  $job = $service->getJobs()->create($search);
  // Wait for job to finish before fetching results
  while (!$job->isDone()) {
    $job->refresh();
  }
  $results = $job->getResults();
}

catch (Exception $e) {
  $messages = array();
  $messages[] = new Splunk_ResultsMessage('EXCEPTION', $e->getMessage());
  // Parse any exceptions from Splunk and display them in Maltego
  foreach ($messages as $message) {
    $maltegoTransform->addException('Splunk encountered an exception: '
				    .htmlspecialchars($message->getType())
				    .'-'
				    .htmlspecialchars($message->getText())
				    );
  }
  $maltegoTransform->throwExceptions();
  exit(1);
}

foreach ($results as $result) {
  if ($result instanceof Splunk_ResultsFieldOrder) {
    // Populate an array with the fieldnames from the results
    $columnNames = $result->getFieldNames();
  }
  elseif (is_array($result) && isset($result["$result_field"])) {
    // Populate an array with the results from the field we want
    $splunk_results[] = $result["$result_field"];
  }
}

//De-dup results
$splunk_results = array_unique($splunk_results);

// Return each result as an entity to Maltego
foreach ($splunk_results as $splunk_result) {
  if (!empty($splunk_result)) {
    $maltegoTransform->addEntity("maltego.IPv4Address", $splunk_result);
  }
}
$maltegoTransform->returnOutput();

?>