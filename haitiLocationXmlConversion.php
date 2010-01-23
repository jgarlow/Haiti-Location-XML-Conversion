<?php

    $catId = 45;
    if(array_key_exists("id", $_GET))
    {
        $catId = $_GET["id"];
    }
    $url = "http://haiti.ushahidi.com/api?task=incidents&by=catid&resp=xml&id=" . $catId;    

     $handle = fopen($url,"rb");
     $xmlFromUshahidi = stream_get_contents($handle);
     fclose($handle);

     $inputXml = simplexml_load_string($xmlFromUshahidi);

     $outputXml = new SimpleXMLElement('<?xml version="1.0" ?><osm version="0.6"></osm>');
     
     foreach($inputXml->payload->incidents->incident as $incident)
     {
         $node = $outputXml->addChild('node');
         $node->addAttribute('id', '-1');
         $node->addAttribute('lat', $incident->location->latitude);
         $node->addAttribute('lon', $incident->location->longitude);
         $tag = $node->addChild('tag');
         $tag->addAttribute('k','title');
         $tag->addAttribute('v',$incident->title);
         $tag = $node->addChild('tag');
         $tag->addAttribute('k','description');
         $tag->addAttribute('v',$incident->description);
         $tag = $node->addChild('tag');
         $tag->addAttribute('k','date');
         $tag->addAttribute('v',$incident->date);
         $tag = $node->addChild('tag');
         $tag->addAttribute('k','ushahidi:id');
         $tag->addAttribute('v', $incident->id);
         if (!empty($incident->location->name))
         {
             $tag = $node->addChild('tag');
             $tag->addAttribute('k','locationName');
             $tag->addAttribute('v',$incident->location->name);
         }
     }
     
     print_r($outputXml->asXML());
?>