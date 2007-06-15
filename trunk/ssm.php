<?php
/*
Plugin Name: Simple Script Markup Processor
*/

add_filter("the_content","ssm_func");

function ssm_func($text){

	
	$pattern = '@(\<ssm\>(.*?)\</ssm\>)@is';
 
    if (preg_match_all($pattern, $text, $matches)) {
        
        for ($i = 0; $i < count($matches[0]); $i++) {
            $innerText=strip_tags($matches[2][$i]);
            $html = "<div class='ssmscript'>" . "\n";
           
            $lines=split("\n", $innerText);
			for ($j = 0; $j < count($lines); $j++) {
				$line=$lines[$j];
				//Now for each line, find its deciding properties.  We do this all at once 
				//since we have to check their values so many times, and some of the tests are
				//time-consuming
				
				$allCaps=isAllCaps($line);
				$startsWithSpace=substr($line, 0,1)==' ';
				$endsWithColon=substr($line, -1,1)==':';
				$startsWithSpaceParen=substr($line, 0,2)==' (';
				$endsWithParen=substr($line, -1,1)==')';
				$startsWithInTorExt=substr($line, 0,4)=='INT.' or substr($line, 0,4)=='EXT.';
				$empty=(trim($line)=='');
				
				  if($allCaps && !$startsWithSpace && $endsWithColon) {
				    if(strstr ($line, "FADE")) {
						$html .="<div class='transition fade'>" . $line . "</div>" . "\n";
				    } else {
						$html .="<div class='transition'>" . $line . "</div>" . "\n";
					}
				  } elseif($startsWithInTorExt) { //todo: add and all caps
					$html .="<div class='scene'>" . $line . "</div>" . "\n";
				  } elseif($startsWithSpace && $allCaps && $endsWithColon) {
				    $html .= "<div class='character'>" . substr($line,1,-1) . "</div>" . "\n";
				  } elseif($startsWithSpaceParen && $endsWithParen) {
				    $html .= "<div class='paren'>" . substr($line,1) . "</div>" . "\n";
				  } elseif($startsWithSpace) {
				    $html .= "<div class='dialogue'>" . substr($line,1) . "</div>" . "\n";
				  } elseif($empty) {
				  //nothing
				  }
				  else {
					$html .="<div class='action'>" . $line . "</div>" . "\n";
				  }
				}
			
       
            $html .="</div>";

            $text = str_replace($matches[0][$i], $html, $text);
        }

    }

    return $text;

}

function isAllCaps($text) {
if (preg_match("/^[\d\sA-Z:\-.'\(\)#;0-9&\/]+$/", $text)) {
   return true;
} 
return false;
}
?>
