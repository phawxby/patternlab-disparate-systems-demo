<?php

// Get the current directory
$currentDir = dirname(__FILE__);
// URI of script
$currentUri = dirname(currentUri());
// Find all files/dirs in current dir
$releases = glob($currentDir . DIRECTORY_SEPARATOR . "*");
// Sort them newest to oldest
uasort($releases, 'sortNewestToOldest');

// Object to store output data
$response = new stdClass();
$response->releases = array();

// Loop each release
foreach($releases as $release)
{
  // Check it's a directory
  if (is_dir($release))
  {
    // Build a response object
    $responseRelease = new stdClass();
    $responseRelease->version = basename($release);
    $responseRelease->date = date("Y-m-d H:i:s", filemtime($release));
    $responseRelease->demo_uri = str_replace($currentDir, $currentUri, $release);

    $scriptPath = $release . DIRECTORY_SEPARATOR . "templates.zip";
    if (file_exists($scriptPath))
    {
      $responseRelease->templates_uri = str_replace($currentDir, $currentUri, $scriptPath);
    }

    $scriptPath = $release . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "script.js";
    if (file_exists($scriptPath))
    {
      $responseRelease->script_uri = str_replace($currentDir, $currentUri, $scriptPath);
    }

    $stylePath = $release . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . "style.css";
    if (file_exists($stylePath))
    {
      $responseRelease->style_uri = str_replace($currentDir, $currentUri, $stylePath);
    }

    $response->releases[] = $responseRelease;
  }
}

// Output our releases as JSON at the end
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
exit;

// Simple function to sort all paths newest to oldest
function sortNewestToOldest($a, $b)
{
  return filemtime(realpath($b)) - filemtime(realpath($a)); 
}

function currentUri()
{
  // https://stackoverflow.com/a/1871778
  return "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}