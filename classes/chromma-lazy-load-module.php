<?php
//abstract interface for chromma lazy load
abstract class Chromma_Lazy_Load_Module
{
  public static function content_lazyload_filter( $content )
  {
      $load_effect = get_option( 'chromma_loadeffect' );
      ($load_effect !== "fadein") ? $lowest_dimension_mod = get_option('chromma-load-dimensions') : $lowest_dimension_mod = "";
  		preg_match_all('/<img .*>/iU', $content, $imgMatches);

  		foreach ($imgMatches[0] as $imgMatch)
  		{
          //for each match of images, get matches of src attribute
					preg_match('/srcset=\"(?<src>.*)\s/iU',$imgMatch, $srcMatch);

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
  		return $content;
  }

}
