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
    preg_match('/wp-image-(?<id>.+)\s/U', $image, $src_url);
    $post_id = (string)$src_url['id'];
    $image_post = get_post($post_id);
    $caption_link = get_post_meta($image_post->ID, 'chroma_caption_hyperlink', true);

    if ( 1 > (int) $width || empty( $caption ) )
        return $content;

  //parse out the desired dimensions and apply the dimensions as an aspect ratio to the figure
  $aspect_ratio = get_option('chromma-load-ar');
  $aspect_ratio = str_replace('-', '', $aspect_ratio);
  $aspect_ratio = str_replace('x', ',', $aspect_ratio);
  $aspect_ratio_array = explode(',', $aspect_ratio);
  $width = $aspect_ratio_array[0];
  $height = $aspect_ratio_array[1];

  $aspectRatio = ($height > 0 && $width > 0) ? ($height / $width) * 100 : 101;
  $aspectThresholdfix = 'height: auto; padding: 0px; max-height: '.$height.'px; max-width: '.$width.'px;';

  $new_caption = (!empty($caption_link)) ? '<a href="'.$caption_link.'" target="_blank" rel="noopener" >' . trim($caption) . '</a>' : trim($caption);
  $new_content = '<figure class="entry-content_figure fig-wcaption">'.$image.'<figcaption class="figcaption">'.$new_caption.'</figcaption></figure>';
	return $new_content;
}
