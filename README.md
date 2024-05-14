# hdhrxml.php
Simple script to generate XMLTV data from your HDHomeRun. 

## No subscription required
You are limited to 24 hours of guide data. You should set a cronjob to run this script ever 6-8 hours.

## Setup
### You should only need to modify 1, possibly 2 variables.
1. If you are unable to resolve hdhomerun.local, then you can manually set the address of your HDHomeRun in $hdhrAddress
2. The location of the output file in $hdhrOutXML. Make sure you have write permissions set properly.

```php
$hdhrAddress = 'hdhomerun.local';
$hdhrOutXML  = '/var/www/html/xmltv/hdhomerun.xml';
```

## Use at your own risk
This was written for my own purposes, therefore there is no error checking. I've been asked to share, so I've posted it here. Lots of things could be done better, but this works and serves its purpose.
