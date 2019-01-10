<?php
//override filter that accepts the_content and processes the caption shortcode for a custom layout
add_filter( 'img_caption_shortcode', 'chroma_img_caption_shortcode', 10, 3 );

function chroma_img_caption_shortcode( $empty, $attr, $content ) {
  extract(shortcode_atts(array(
      'id'	=> '',
      'align'	=> '',
      'width'	=> '',
      'caption' => ''
    ), $attr));
    //render the image
    $image = do_shortcode( $content );

    //match post id and pull out the caption hyperlink metadata
    preg_match('/wp-image-(?<id>.+)\s/U', $image, $img_id);
    $post_id = (string)$img_id['id'];
    $image_post = get_post($post_id);
    $caption_link = get_post_meta($image_post->ID, 'chroma_caption_hyperlink', true);

    if ( 1 > (int) $width || empty( $caption ) )
        return $content;

  //parse out the desired dimensions and apply the dimensions as an aspect ratio to the figure
  preg_match('/class="(?<class>.+)"\s/U', $image, $imgClasses);
  preg_match('/src="([^"]*)"/', $image, $src_url);
  $src_url = str_replace('src="', '', $src_url[0]);
  $src_url = str_replace('"', '', $src_url);

  $aspectRatio = '';
  $width = 1;
  $height = 1;
  if (strpos($imgClasses['class'],'size-full') !== false) {
    list($width, $height) = getimagesize($src_url);
    $aspectRatio = ( $height > 0 && $width > 0) ? ($height / $width) * 100 : 60;
  } elseif(!empty(get_option('chromma-load-ar'))) {
    $aspect_ratio = get_option('chromma-load-ar');
    $aspect_ratio = str_replace('-', '', $aspect_ratio);
    $aspect_ratio = str_replace('x', ',', $aspect_ratio);
    $aspect_ratio_array = explode(',', $aspect_ratio);
    $width = $aspect_ratio_array[0];
    $height = $aspect_ratio_array[1];
    $aspectRatio = ($height > 0 && $width > 0) ? ($height / $width) * 100 : 60;
  }

  $new_caption = (!empty($caption_link)) ? '<a href="'.$caption_link.'" target="_blank" rel="noopener" >' . trim($caption) . '</a>' : trim($caption);
  $new_content = '<figure class="entry-content_figure fig-wcaption" style="padding-bottom:calc('.$aspectRatio.'% + 36px)">'.$image.'<figcaption class="figcaption">'.$new_caption.'</figcaption></figure>';
	return $new_content;
}
