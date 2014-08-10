Munk
====

Query Splunk Data Using Maltego

## Abstract
Maltego is only as useful as the data you provide for it, and sometimes that data lives in places that can be a little bit tricky to access. This transform example provides a way for you to perform a Splunk query and return the search results as entities into Maltego. 

## Example Code
The example transform I've included works on a made up dataset of network traffic logs inside of a Splunk database. The sample dataset is shown below:

![Alt text](/screenshots/SplunkData.png?raw=true "Splunk Sample Dataset")

Let's say you wanted to run a query in Maltego and see all of the destination IP addresses that have been acceessed by "192.168.1.100". You could return this data in Splunk by using the query:
```
index=main src_ip=192.168.1.100 | fields dst_ip
```
The dst_ips that you would expect in the results would be 4.2.2.2, 66.211.169.3, 8.8.8.8, 192.168.3.30, 192.168.2.20

By using this code, you can replicate that query inside of a Maltego transform and return those results as linked entities
![Alt text](/screenshots/FromSourceIPToDestinationIPs.png?raw=true "From Source IP To Destination IPs")

## Installation & Setup
This guide assumes that you already have a functioning Maltego installation, complete with an iTDS server. In addition to that, the transform requires the latest version of the [Splunk PHP API](http://dev.splunk.com/view/php-sdk/SP-CAAAEJM).

The Maltego transform will require some code from the Splunk API. You'll need Splunk.php, settings.php, settings.default.php and the entire "Splunk/" folder to be in a place where the transform can access it.

1. Copy settings.default.php to settings.local.php 
2. Edit settings.local.php and populate the $SplunkExamples_connectArguments array with valid Spunk credentials. 

## Transform code
You can use FromSourceIPToDestinationIPs.php as a template to write other Splunk transforms. 
Although it's possible to return more than one type of entity per transform, I prefer to only extract one field worth of data per transform and that field is defined here:
```
$result_field = 'dst_ip';
```

The try block contains the actual query that will be executed. 
```
// Splunk search query
$search = 'search index=main src_ip='.$src_ip
  .' earliest='.$start_date
  .' latest='.$end_date
  .' | fields '.$result_field;
```

