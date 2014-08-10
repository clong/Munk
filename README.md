Munk
====

Query Splunk Data Using Maltego

## Abstract
Maltego is only as good as the data you provide for it, and sometimes that data lives in places that can be a little bit tricky to access. This transform example provides a way for you to perform a Splunk query and return the results as an entity into Maltego. 

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

The Maltego transform code will require a few files from the Splunk API. Splunk.php, settings.php and settings.default.php. You'll need to copy settings.default.php to settings.local.php and edit the file and fill out the $SplunkExamples_connectArguments array with valid Spunk credentials. 
