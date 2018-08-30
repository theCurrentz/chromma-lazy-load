<?php
//abstract interface for chromma lazy load
class Chromma_Lazy_Load_Module {

  public function __construct() {
  }

  public static function apply_aspect_ratio($img, $figure) {
    //get options
    $load_effect = get_option( 'chromma_loadeffect' );
    $lowest_dimension_mod = ($load_effect !== "fadein") ? get_option('chromma-load-dimensions') : "";
    $aspect_ratio = get_option('chromma-load-ar');
    //parse out the desired dimensions and apply the dimensions as an aspect ratio to the figure
    $aspect_ratio = str_replace('x', ',', $aspect_ratio);
    $aspect_ratio = str_replace('-', '', $aspect_ratio);
    $aspect_ratio_array = explode(',', $aspect_ratio);
    $width = $aspect_ratio_array[0];
    $height = $aspect_ratio_array[1];
    //list($width, $height) = getimagesize($src);
    $aspectRatio = ($height > 0 && $width > 0) ? ($height / $width) * 100 : 101;

    //if aspect ratio is larger than desired, we'll fallback to a figure/img relationship w/o a set aspec ratio
    if($aspectRatio > 100 )
       $img->setAttribute('style', 'position: relative');

    $aspectRatio = 'padding-bottom: '. $aspectRatio .'%;';
    $figure->setAttribute('style', $aspectRatio);
  }

  public static function content_lazyload_filter( $content ) {

    libxml_use_internal_errors(true);
    //initialize a dom document for easier more accurate parsing
    $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
    $dom = new DOMDocument();
    $dom->loadHTML($content);

    $dom->encoding = 'utf-8';
    $xpath = new DOMXpath($dom);
    //xpath query targets all images that are children of the div css class entry-content
    $unwrappedImgs = $xpath->query("//img");
    foreach ($unwrappedImgs as $img) {
      //get attrobite data
      $imgSrc = $img->getAttribute('src');
      $imgSrcSet = $img->getAttribute('srcset');
      $imgClasses = (string)$img->getAttribute('class');
      //create a figure & set class to entry-content_figure
      if (($img->parentNode->nodeName != 'figure') && (strpos($imgClasses,'aalb-pa-product-image-source') <= -1)  && (strpos($imgSrc,'amazon-adsystem') <= -1)) {
        $figure = $dom->createElement('figure');
        $figure->setAttribute('class','entry-content_figure');
        //replace $img with wrapper figure then appendChild the $img back into the figure
        $img->parentNode->replaceChild($figure, $img);
        $figure->appendChild($img);
      } else
        $figure = $img->parentNode;

      //check for exemptions
      if ( strpos($imgClasses,'aalb-pa-product-image-source') > -1 || strpos($imgSrc,'amazon-adsystem') > -1) {
        continue;
      } elseif (strpos($imgClasses,'size-full') < 0) {
        self::apply_aspect_ratio($img, $figure);
      } elseif(strpos($img->parentNode->getAttribute('class'), 'fig-wcaption') > -1)  {
        continue;
      } else {
        list($width_full, $height_full) = getimagesize($img->getAttribute('src'));
        if ( $height_full > 0 && $width_full > 0) {
          $aspectRatio_full = 'padding-bottom: ' . ($height_full / $width_full) * 100  ."%";
          $figure->setAttribute('style', $aspectRatio_full);
        } else {
          $figure->setAttribute('style', 'position: relative; padding-bottom: 0px; height: auto;');
          $img->setAttribute('style', 'position: relative; height: auto;');
        }
      }

      //set img src/datasrc for lazyload handling
      $img->removeAttribute('srcset');
      $img->setAttribute('data-src', $imgSrc);
      if (!empty($imgSrcSet)) {
        $img->setAttribute('data-srcset', $imgSrcSet);
      }

      //get options
      $load_effect = get_option( 'chromma_loadeffect' );
      $lowest_dimension_mod = ($load_effect !== "fadein") ? get_option('chromma-load-dimensions') : "";
      //set img src to a blank transparent gif or low res blur
      if($load_effect === "blur") {
        global $wpdb;
        $attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $imgSrc))[0];
        $img_blur = wp_get_attachment_image_src($attachment_id, $lowest_dimension_mod)[0];
        $img->setAttribute('src', $img_blur);
      } else {
        $img->setAttribute('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
      }
      $imgClasses = $img->getAttribute('class') . ' lazyload-img llreplace';
      $img->setAttribute('class', $imgClasses);

      //save the node to the $dom object
      $dom->saveHTML($img);
      $dom->saveHTML($figure);
    }

    //save and return
    $content = preg_replace('/^<!DOCTYPE.+?>/','',str_replace(array('<html>', '</html>', '<body>', '</body>'),array('', '', '', ''),$dom->saveHTML()));
    return $content;
  }

} //end Chromma_Lazy_Load_Module
