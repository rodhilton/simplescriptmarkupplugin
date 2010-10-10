<?php
/*
Plugin Name: Simple Script Markup Processor
*/

add_filter("the_content","ssm_func");

function ssm_func($text){
  $renderer = RendererFactory::getRenderer(is_feed());
  $outputter = new ScriptOutputter($renderer);
  return $outputter->output($text);
}

class ScriptOutputter {

  private $PATTERN = '@(\<ss[fm]\>(.*?)\</ss[fm]\>)@is';  
  private $scriptRenderer;

  function __construct($scriptRenderer) {
    $this->scriptRenderer = $scriptRenderer;
  }

  function output($text) {
    if (preg_match_all($this->PATTERN, $text, $matches)) {
      for ($i = 0; $i < count($matches[0]); $i++) {
        $innerText=strip_tags($matches[2][$i], '<span>');
        $html = $this->scriptRenderer->open();
           
        $lines=split("\n", $innerText);
        foreach ($lines as $line) {
		      //Now for each line, find its deciding properties.  We do this all at once 
		      //since we have to check their values so many times, and some of the tests are
		      //time-consuming		
		      $allCaps=$this->isAllCaps($line);
		      $startsWithSpace=substr($line, 0,1)==' ';
		      $endsWithColon=substr($line, -1,1)==':';
		      $startsWithSpaceParen=substr($line, 0,2)==' (';
		      $endsWithParen=substr($line, -1,1)==')';
		      $startsWithInt=substr($line, 0,4)=='INT.';
		      $startsWithExt=substr($line, 0,4)=='EXT.';
		      $empty=(trim($line)=='');
		
	        if($allCaps && !$startsWithSpace && $endsWithColon) {
	          if(strstr($line, "FADE IN")) {
              $html .= $this->scriptRenderer->fade($line);
	          } else {
              $html .= $this->scriptRenderer->transition($line);
		        }
	        } elseif($allCaps && ($startsWithInt || $startsWithExt)) {
              $html .= $this->scriptRenderer->scene($line);
	        } elseif($startsWithSpace && $allCaps && $endsWithColon) {
              $html .= $this->scriptRenderer->character(substr($line,1,-1));
	        } elseif($startsWithSpaceParen && $endsWithParen) {
              $html .= $this->scriptRenderer->paren(substr($line,1));
	        } elseif($startsWithSpace) {
              $html .= $this->scriptRenderer->dialogue(substr($line,1));
	        } elseif($empty) {
	          //nothing
	        } else {
            $html .= $this->scriptRenderer->action($line);
	        }
		    }
        $html .= $this->scriptRenderer->close();

        $text = str_replace($matches[0][$i], $html, $text);
      }
    }

    return $text;
  }

  private function isAllCaps($text) {
    if (preg_match("/^[\d\sA-Z:\-.'\(\)#;,0-9&\/’]+$/", $text)) {
      return true;
    } 
    return false;
  }
}

interface ScriptRenderer {
  public function open();
  public function fade($line);
  public function transition($line);
  public function scene($line);
  public function character($line);
  public function paren($line);
  public function dialogue($line);
  public function action($line);
  public function close();
}

class NormalRenderer implements ScriptRenderer {
  public function open() {
    return "<div class='ssfscript'>\n";
  }

  public function fade($line) {
    return "<p class='transition fade'>{$line}</p>" . "\n";
  }

  public function transition($line) {
    return "<p class='transition'>{$line}</p>" . "\n";
  }

  public function scene($line) {
    return "<p class='scene'>{$line}</p>" . "\n";
  }

  public function character($line) {
    return "<p class='character'>{$line}</p>" . "\n";
  }

  public function paren($line) {
    return "<p class='paren'>{$line}</p>" . "\n";
  }

  public function dialogue($line) {
    return "<p class='dialogue'>{$line}</p>" . "\n";
  }

  public function action($line) {
    return "<p class='action'>{$line}</p>" . "\n";
  }

  public function close() {
    return "</div>";
  }
}

class PlainTextRenderer implements ScriptRenderer {
  private $WIDTH=72;
  private $CHARACTER_MARGIN=25;
  private $DIALOGUE_MARGIN=15;
  private $PAREN_MARGIN=17;

  public function open() {
    return "<pre>\n";
  }

  public function fade($line) {
    return "{$this->wrap($line)}\n";
  }

  public function transition($line) {
    $indent = $this->WIDTH - strlen($line);
    return str_repeat(" ", $indent) . $line . "\n";
  }

  public function scene($line) {
    return "\n{$this->wrap($line)}\n";
  }

  public function character($line) { 
    $wrapped = $this->wrap($line, 0, $this->WIDTH-($this->CHARACTER_MARGIN*2));
    $wrapped_split = explode("\n", $wrapped);
    $str = "\n";
    foreach ($wrapped_split as $wrapline) {
      $indent = ($this->WIDTH - strlen($wrapline)) / 2;
      $str .= str_repeat(" ", $indent) . $wrapline . "\n";
    }
    return $str;
  }

  public function paren($line) {
    return str_repeat(" ", $this->PAREN_MARGIN) . "{$this->wrap($line, $this->PAREN_MARGIN)}\n";
  }

  public function dialogue($line) {
    return str_repeat(" ", $this->DIALOGUE_MARGIN) . "{$this->wrap($line, $this->DIALOGUE_MARGIN)}\n";
  }

  public function action($line) {
    return "\n{$this->wrap($line)}\n";
  }

  public function close() {
    return "</pre>";
  }

  private function wrap($line, $indent=0, $width=null) {
    if($width===null)
        $width = $this->WIDTH;

    $line = html_entity_decode($line);
    $line = htmlspecialchars_decode($line);
    $line = str_replace("&#8217;","'",$line);
    $line = str_replace("&#8211;","-",$line);
    $line = str_replace("&#8221;","\"",$line);
    $line = str_replace("&#8220;","\"",$line);
    $line = str_replace("&#8230;","...",$line);

    $wrap = wordwrap($line, $width-$indent*2, "\n" . str_repeat(" ", $indent), true);
    $wrap = htmlspecialchars($wrap);
    return "{$wrap}";
  }
}

class RendererFactory {
  public static function getRenderer($feed) {
    if($feed) {
      return new PlainTextRenderer();
    } else {
      return new NormalRenderer();
    }
  }
}
?>
