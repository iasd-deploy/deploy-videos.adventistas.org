<?php


class IASD_Shortcodes {
	static function Register() {
		global $IASD_Shortcodes_isFAQOpen, $IASD_Shortcodes_questionCount, $IASD_Shortcodes_faqCount;
		$IASD_Shortcodes_isFAQOpen = false;
		$IASD_Shortcodes_questionCount = 0;
		$IASD_Shortcodes_faqCount = 0;
		add_shortcode('faqs', array(__CLASS__, 'FAQ'));
        add_shortcode('asks', array(__CLASS__, 'ASK'));
        add_shortcode('iasd_gallery', array(__CLASS__, 'Gallery'));
        //add_filter('post_gallery', array(__CLASS__, 'Gallery'), 10, 2);
	}

	static function FAQ ( $atts, $content = null ) {

		if(strpos($content, '[asks') && strpos($content, '/asks')) {
			global $IASD_Shortcodes_faqCount, $IASD_Shortcodes_isFAQOpen;
			$IASD_Shortcodes_faqCount++;
			$IASD_Shortcodes_isFAQOpen = true;

			$content = substr($content, strpos($content, '[asks'));
			$content = substr($content, 0, strrpos($content, '[/asks]') + 7);
			$content = preg_replace('(\[/asks\][^\[]+\[asks)ms', "[/asks]\r\n[asks", $content);

			$content = do_shortcode($content);

			$content = '<dl id="faq-accordion-'.$IASD_Shortcodes_faqCount.'" class="faq mar-top-30">'.$content.'</dl>';

			$IASD_Shortcodes_isFAQOpen = false;
		}

		return $content;
	}

	static function ASK ( $atts, $answer = null ) {
		global $IASD_Shortcodes_isFAQOpen;

		$question = (isset($atts['q'])) ? $atts['q'] : '';
		$processed_answer = do_shortcode($answer);

		$html = '';

		if($IASD_Shortcodes_isFAQOpen && $question && $processed_answer) {
			global $IASD_Shortcodes_questionCount, $IASD_Shortcodes_faqCount;
			$IASD_Shortcodes_questionCount++;
			$html_question = '<dt><a data-toggle="collapse" data-parent="#faq-accordion-'.$IASD_Shortcodes_faqCount.'" href="#question-'.$IASD_Shortcodes_questionCount.'" title="'.__('Clique para ler a resposta', 'iasd').'">'.$question.'</a></dt>';
			$html_answer = '<dd id="question-'.$IASD_Shortcodes_questionCount.'" class="panel-collapse collapse">'.$processed_answer.'</dd>';
			$html = $html_question.$html_answer;

		} else {
			$html = $question . $processed_answer;
		}

		return $html;
	}
//post_gallery
    static function Gallery($current, $attr) {

        if(isset($current['id'])) {
            $args = array(
                'post_type' => 'attachment',
                'numberposts' => -1,
                'post_status' => null,
                'post_parent' => $current['id']
            );
        } else {
            $args = array('post_type' => 'attachment', 'include' => $attr['include']);
        }
        $attachments = get_posts( $args );

        if(!count($attachments))
            return $current;

        ob_start();
?>
<div class="iasd-images-gallery">
    <div class="iasd-images-gallery-thumbs owl-carousel hidden-xs">
    <?php
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $thumb_src = wp_get_attachment_image_src($attachment->ID, 'thumb_140x90');
                echo '<div class="item">';
                echo '<img data-src="'.$thumb_src[0].'" class="img-responsive lazyOwl" alt="'.apply_filters( 'the_title', $attachment->post_title ).'" />';
                echo '</div>';
            }
        }
        ?>
    </div>
    <div class="iasd-images-gallery-pics owl-carousel">
        <?php
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $image_src  = wp_get_attachment_image_src($attachment->ID, 'thumb_730xAUTO');
                $auxHeader  = get_headers($image_src[0],1);
                $header     = $auxHeader[0];
                $img_att = wp_prepare_attachment_for_js($attachment->ID);

                // Hack para funcionamento de imagens upadas antes dessa atualização do thumb_730xAUTO
                if (strpos($header, '404')) {
                    $image_src = wp_get_attachment_image_src($attachment->ID, 'full');
                }

                if ( $img_att['caption'] ) {
                    $caption_class = 'wp-caption';
                }

                echo '<div class="item ' . $caption_class . ' ">';
                echo '<img data-src="'.$image_src[0].'"class="img-responsive lazyOwl" alt="'.apply_filters( 'the_title', $attachment->post_title ).'"/>';
                if ( $img_att['caption'] ) {
                    echo '<p class="wp-caption-text" >' . $img_att['caption'] . '</p>';
                }
              
                // INCLUI LINK DE DOWNLOAD
                // echo '<a href="'. $img_att['url'] .'" download class="text-right" >Download</a>';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>
<?php
        $current = ob_get_contents();
        ob_end_clean();

        return $current;
    }
}

IASD_Shortcodes::Register();
