<?php

const ITEM_COUNT = 99;

require_once('workflows.php');
$w = new Workflows();

// $query = "superman";
$query = trim(@$argv[1]);
// $query_encoded = urlencode( $query );

$source = "http://apicache.vudu.com/api2/claimedAppId/myvudu/format/application*2Fjson/callback/DirectorSequentialCallback/_type/catalogItemTitleSearch/count/".ITEM_COUNT."/onlyConvertible/true/titleMagic/".$query;

$json = $w->request( $source );
$json = substr( $json, strlen( 'DirectorSequentialCallback(' ) );
$json = substr($json, 0, -1);
// echo '<textarea style="width: 100%; height: 555px">'.$json.'</textarea>';

$data = json_decode( $json );

$movies = $data->catalogItemTitle;
$total = count($movies);

$cnt = 0;

if ( $total > 0 ):
	foreach( $movies as $movie ):
		
		if($movie->allowPhysicalCopyConversion[0] == true){
			$cnt++;
			
			$id = $movie->catalogItemId[0];
			$contentId = $movie->contentId[0];
			$discType = ($movie->discType[0] == "dvd") ? "DVD" : "Blu-Ray";
			$title = trim($movie->title[0])." (".$movie->year[0].") [".$discType."]";
			// $url = "http://www.vudu.com/movies/#!content/".$contentId;
			// $poster = "http://images2.vudu.com/poster2/".$contentId."-s";
			$icon = "icon.png";
			
			$sub = "(".$cnt." of ".$total;
			if($total == ITEM_COUNT){
				$sub = $sub."+)";
			} else {
				$sub = $sub.")";
			}
			$sub = $sub." Available: ";
			
			if($movie->sameQualityLabel[0] == "sd"){
				$sub = $sub."SD";
				if(array_key_exists('upgradeQualityLabel', $movie) && $movie->upgradeQualityLabel[0] == "hdx"){
					$sub = $sub." | HD";
				}
			} else if($movie->sameQualityLabel[0] == "hdx"){
				$sub = $sub." HD";
			}
			
			// echo $title.'<br />';
			// echo $sub.'<br />';
			// echo '<br />';
			
			$w->result(
				$cnt.".".time(),
				$contentId."|".urlencode($query),
				"$title",
				"$sub",
				"$icon"
			);
			
			// echo '<textarea style="width: 100%; height: 2em;">'.$movie->title[0].'</textarea>';
			// echo '<br />';
		}
		
	endforeach;
		
else:
	$w->result(
		"vududirect-noentry",
		"0|".urlencode($query),
		"No titles found",
		"Try a different search or select to check vudu's website",
		"icon.png"
	);
endif;

echo $w->toxml();
// echo '<textarea style="width: 100%; height: 25em;">'.$w->toxml().'</textarea>';

?>