<?php

/////////////////////
//
// JSON rows are read from STDIN and output is JSON
//
////////////////////

require('vendor/autoload.php');
use Wikibase\JsonDumpReader\JsonDumpFactory;
use League\Geotools\Coordinate\Coordinate;


$factory = new JsonDumpFactory();

$t = 0;

while ($jsonLine = fgets(STDIN)) {
    $t++;
    if ($t % 10000 == 0) {
        $p = $t / 1000;
        fwrite(STDERR, $p . " thousand rows\n");
    }


    // remove commas and \n from rows end
    $cleanLine = rtrim($jsonLine, "\n,");

    $obj = json_decode($cleanLine);

     /* check if P625 exists and find if at least one coordinate
        is on Earth (globe Q2) and is inside our bounding box
      */

     if (isset($obj->claims->P625)) {
        $count = count($obj->claims->P625);
        for ($x = 0; $x < $count; $x++) {
            if (isset($obj->claims->P625[$x]->mainsnak->datavalue->value->globe) &&    
                substr($obj->claims->P625[$x]->mainsnak->datavalue->value->globe, -2) == 'Q2') {
                $id = $obj->id;
                $lon = $obj->claims->P625[$x]->mainsnak->datavalue->value->longitude;
                $lat = $obj->claims->P625[$x]->mainsnak->datavalue->value->latitude;

                print $jsonLine."\n";
                break;
            }
        }
    }
}
