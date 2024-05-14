<?php
error_reporting(E_ALL ^ E_NOTICE);  
//--------------------------
$hdhrAddress = 'hdhomerun.local';
//$hdhrAddress = '192.168.0.254';
$hdhrOutXML  = '/var/www/html/xmltv/hdhomerun.xml';
//--------------------------

$hdhrInfo  = json_decode(file_get_contents('http://'.$hdhrAddress.'/discover.json'), true);
$hdhrChans = json_decode(file_get_contents($hdhrInfo['LineupURL']), true);
$devAuth     = $hdhrInfo['DeviceAuth'];

foreach ($hdhrChans as $chan) {
  if (!$chan['Favorite'])
    continue;
  $chanData = getChanData($chan['GuideNumber']);
  $xmlChans .= makeChannelXML($chanData);
  $xmlGuide .= makeGuideXML($chanData);
}

$fp = fopen($hdhrOutXML, 'w');
fwrite($fp, "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<!DOCTYPE tv SYSTEM \"xmltv.dtd\">\n\n<tv>\n");
fwrite($fp, $xmlChans);
fwrite($fp, $xmlGuide);
fwrite($fp, "</tv>\n");


function makeGuideXML($chanData) {
  if ($chanData == '')
    return;

  $channel   = $chanData[0]['GuideName'];
  $guideData = $chanData[0]['Guide'];
  $xml = '';

  foreach ($guideData as $g) {
    $start = date('YmdHis O', $g['StartTime']);
    $stop  = date('YmdHis O', $g['EndTime']);
    $xml  .= '  <programme channel="'.$channel.'" start="'.$start.'" stop="'.$stop.'">'."\n";
    $xml  .= '    <title lang="en">'.htmlspecialchars($g['Title']).'</title>'."\n";

    if ($g['OriginalAirdate'] <> '') {
      $orig = date('YmdHis O', $g['OriginalAirdate']);
      $xml .= '    <previously-shown start="'.$orig.'" />'."\n";
    }
    $xml   .= '    <icon src="'.$g['ImageURL'].'" />'."\n";

    if ($g['EpisodeTitle'] <> '')
      $xml .= '    <sub-title lang="en">'.htmlspecialchars($g['EpisodeTitle']).'</sub-title>'."\n";

    $xml   .= '    <desc lang="en">'.htmlspecialchars($g['Synopsis']).'</desc>'."\n";

    if ($g['EpisodeNumber'] <> '') {
      $xml .= '    <category lang="en">Series</category>'."\n";
      $xml .= '    <episode-num system="onscreen">'.$g['EpisodeNumber'].'</episode-num>'."\n";

    }

    if ($g['Filter'][0] <> '')
      $xml .= '    <category lang="en">'.$g['Filter'][0].'</category>'."\n";

    $xml   .= '    <audio>'."\n";
    $xml   .= '      <stereo>stereo</stereo>'."\n";
    $xml   .= '    </audio>'."\n";
    $xml   .= '    <subtitles type="teletext" />'."\n";
    $xml   .= '  </programme>'."\n";
  }
  return $xml;
}


function getChanData($chan) {
  global $devAuth;
  $data = json_decode(file_get_contents("https://api.hdhomerun.com/api/guide?DeviceAuth=".$devAuth."&Channel=".$chan), true);
  return $data;
}

function makeChannelXML($chanData) {
  if ($chanData[0]['GuideName'] == '')
    return;

  $xml  = '  <channel id="'.$chanData[0]['GuideName'].'">'."\n";
  $xml .= '    <display-name>'.$chanData[0]['GuideName'].'</display-name>'."\n";
  $xml .= '    <display-name>'.$chanData[0]['GuideNumber'].'</display-name>'."\n";
 
  if ($chanData[0]['Affiliate'] <> '')
    $xml .= '    <display-name>'.$chanData[0]['Affiliate'].'</display-name>'."\n";

  if ($chanData[0]['ImageURL'] <> '')
    $xml .= '    <icon src="'.$chanData[0]['ImageURL'].'" />'."\n";

  $xml .= '  </channel>'."\n";
  return $xml;
} 
