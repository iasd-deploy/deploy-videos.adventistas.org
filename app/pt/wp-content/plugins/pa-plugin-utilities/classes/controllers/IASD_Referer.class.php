<?php
/**
 * Created by IntelliJ IDEA.
 * User: sidferreira
 * Date: 07/04/14
 * Time: 14:21
 */

if( ! is_admin() ) {
    add_action( 'wp_head', array( 'IASD_Referer', 'Identify' ), 99 );

}
add_action( 'wp_ajax_iasd_referer_render', array( 'IASD_Referer', 'Render' ) );
add_action( 'wp_ajax_nopriv_iasd_referer_render', array( 'IASD_Referer', 'Render' ) );

class IASD_Referer {
    public static function Identify() {
        if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
            $full_url = $_SERVER['HTTP_REFERER'];
            $url_referer = parse_url( $full_url, PHP_URL_HOST );
            $url_request = parse_url( get_bloginfo( 'url' ), PHP_URL_HOST );

            $domain_referer = self::FilterDomain( $url_referer );
            $domain_request = self::FilterDomain( $url_request );

           if ( $domain_referer == $domain_request && $url_referer != $url_request ) {
               $url = base64_encode( $full_url );
?>
<script>
        jQuery(document).ready(function() {
            jQuery.get(ajaxurl + "?action=iasd_referer_render&url=<?php echo $url; ?>",
                function(referer_html) {
                    if(referer_html.slice(-1) == '0')
                        referer_html = referer_html.substring(0, referer_html.length - 1);
                    jQuery("body").append(referer_html);
                    var jqThis = jQuery('.iasd-plugin-return_page');
                    jqThis.removeClass('collapsed');
                    jqThis.find('.toggle-visibility-link').attr("title", "Clique para fechar este link");
                    setTimeout(function() {
                        jqThis.addClass('collapsed');
                        jqThis.find('.toggle-visibility-link').attr("title", "Clique para visualizar o link da página anterior");
                    }, 5000);

                }, 'html');
        });
</script>
<?php
            }
        }
    }


    static function FilterDomain( $host )
    {
        if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $regs ) ) {
            return $regs['domain'];
        }
        return false;
    }

    public static function Render() {
        $url = base64_decode( $_REQUEST['url'] );
        if ( ! empty( $url ) ){
            if(substr($url, -1) != '/')
                $url .= '/';

            $requestResult = wp_remote_get( $url . '?iasd_title_only', array( 'timeout' => 120 ) );
            if ( substr( $requestResult['body'], -1 ) == '0' )
                $requestResult['body'] = substr( $requestResult['body'], 0, -1 );
            $requestBody = $requestResult['body'];
            preg_match( "/<title>([^<]+)<\/title>/", $requestBody, $matches );
            if ( count( $matches ) == 2 ) {
                $title = trim( $matches[1] );
                $trimmedTitle = apply_filters( 'trim', $title, 45 );
?>  
                <script> 
                    function hideUnhide(){
                        var element = document.getElementById("btnhide");
                        if ( element.className == "iasd-plugin-return_page visible-md visible-lg" ){
                            element.className = "iasd-plugin-return_page visible-md visible-lg collapsed";
                        } else {
                            element.className = "iasd-plugin-return_page visible-md visible-lg";
                        }
                    }               
                </script>
                <div class="iasd-plugin-return_page visible-md visible-lg collapsed" id="btnhide" onclick="javascript:hideUnhide();">
                    <a href="javascript:void(0)"
                       data-collapsed="<?php _e( 'Clique para visualizar o link da página anterior', 'iasd' ); ?>"
                       data-expanded="<?php _e( 'Clique para fechar este link', 'iasd' ); ?>"
                       title="<?php _e( 'Clique para visualizar o link da página anterior', 'iasd' ); ?>"
                       class="toggle-visibility-link"><?php _e( 'Esconder', 'iasd' );?></a>
                    <a href="<?php echo $url; ?>" title="&lt;<?php echo __( 'Clique para voltar para', 'iasd' ) . ' ' . $title; ?>&gt;" class="page-link"><?php echo $trimmedTitle; ?></a>
                </div>
<?php
            }
        }
    }
}
