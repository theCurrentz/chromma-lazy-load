<?php
//abstract interface for chromma lazy load
abstract class Chromma_Lazy_Load_Module
{
  public static function content_lazyload_filter( $content )
  {
      $load_effect = get_option( 'chromma_loadeffect' );
      $lowest_dimension_mod = ($load_effect !== "fadein") ? get_option('chromma-load-dimensions') : "";
      $aspect_ratio =  get_option('chromma-load-ar');

      //match all images
  		preg_match_all('/<img .*>/iU', $content, $imgMatches);

      //iterating through all the images, swap the image src and srcset attributes with data-src and data-srcset
  		foreach ($imgMatches[0] as $imgMatch) {
          //for each match of images, get matches of src attribute
					preg_match('/src=\"(?<src>.*)\s/iU',$imgMatch, $srcMatch);

					//find replace sourcese with a data bound srcset
					if (!empty($srcMatch['src']))
					{
              if($load_effect == 'fadein')
              {
                $data_src = "data-src='".$srcMatch['src']."'";
                $imgMatchModifiedsrc = preg_replace('/src=\".*\"/iU' , $data_src, $imgMatch);
                $imgMatchModified = str_replace("srcset=", "srcset='' data-srcset=", $imgMatchModifiedsrc);
              }
              else
              {
                //default length of the format(e.g. .jpg, .png) type suffix
                $formatLength = 4;
                //if .jpeg, extend to 5
                if(strpos($srcMatch['src'], 'jpeg') == true)
                {
                  $formatLength = 5;
                }

  							//store length of source
  							$srcMatchLength = strlen($srcMatch['src']);
  							//if grab everything except the last 4 characters
  							$srcMatchSub = substr( $srcMatch['src'] , 0 , ($srcMatchLength - $formatLength) );
  							//detect gif format
  							$srcMatchGif = strpos($srcMatch['src'], 'gif');

  							//attach the a modifier for lowest dimensions available to you directly before the format(e.g. .jpg) type suffix
  							$srcMatchSubEnd = $lowest_dimension_mod . substr( $srcMatch['src'], $srcMatchLength - $formatLength, ( $srcMatchLength ) );

                //if src is a gif, do not to attach lowest dimension modifer
  							if ($srcMatchGif !== false)
  							{
  								$imgMatchModifiedsrc = preg_replace('/src=\".*\"/iU' , "src='{$srcMatch}' data-src='{$srcMatch}'", $imgMatch);
                  $imgMatchModified = str_replace("srcset=", "srcset='{$srcMatchSub}{$srcMatchSubEnd}' data-srcset=", $imgMatchModifiedsrc);
  							}
                //otherwise
                else
                {
  							   $imgMatchModifiedsrc = preg_replace('/src=\".*\"/iU' , "src='{$srcMatchSub}{$srcMatchSubEnd}'", $imgMatch);
                   $imgMatchModified = str_replace("srcset=", "srcset='{$srcMatchSub}{$srcMatchSubEnd}' data-srcset=", $imgMatchModifiedsrc);
  							}
              }
              $content = str_replace($imgMatch, "{$imgMatchModified}", $content);
					}
  		}

  		$content = str_replace('<img class="', '<img class="lazyload-img llreplace ', $content);

      $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
      $dom = new DOMDocument();
      $dom->loadHTML($content);
      $dom->encoding = 'utf-8';
      $xpath = new DOMXpath($dom);
      //xpath query targets all images that are children of the div css class entry-content
      $unwrappedImgs = $xpath->query("//body/img");
      foreach ($unwrappedImgs as $img) {
        //create a figure & set class to entry-content_figure
        $figure = $dom->createElement('figure');
        $figure->setAttribute('class','entry-content_figure');

        //replace $img with wrapper figure then appendChild the $img back into the figure
        $img->parentNode->replaceChild($figure,$img);
        $figure->appendChild($img);

        //parse out the desired dimensions and apply the dimensions as an aspect ratio to the figure
        $aspect_ratio = str_replace('x', ',', $aspect_ratio);
        $aspect_ratio = str_replace('-', '', $aspect_ratio);
        $aspect_ratio = explode(',', $aspect_ratio);
        $width = $aspect_ratio[0];
        $height = $aspect_ratio[1];

        //list($width, $height) = getimagesize($src);
        $aspectRatio = ($height > 0 && $width > 0) ? ($height / $width) * 100 : 101;

        //if aspect ratio is larger than desired, we'll fallback to a figure/img relationship w/o a set aspec ratio
        if($aspectRatio > 80 ) {
           $img->setAttribute('style', 'position: relative');
        }
        $aspectThresholdfix = ($aspectRatio > 58) ? 'height: auto; padding: 0px; max-height: '.$height.'px; max-width: '.$width.'px;' : 'padding-bottom: '. $aspectRatio .'%;';
        $styles = $aspectThresholdfix;
        $figure->setAttribute('style', $styles);
        //set img src to a blank transparent gif
        $img->setAttribute('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        //save the node to the $dom object
        $dom->saveHTML($img);
        $dom->saveHTML($figure);
      }

      //save and return
      $content = preg_replace('/^<!DOCTYPE.+?>/','',str_replace(array('<html>', '</html>', '<body>', '</body>'),array('', '', '', ''),$dom->saveHTML()));
      return $content;

  }

}
