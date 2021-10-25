<?php

abstract class IASD_AdaptableWidget extends WP_Widget {
    private $instance = array();

    function getInstance() {
        return $this->instance;
    }

    function setInstance($instance) {
        $this->instance = $instance;
    }

    function getInstanceData($param) {
        if(is_array($this->instance)) {
            if(isset($this->instance[$param])) {
                return $this->instance[$param];
            }
        }

        return '';
    }

    function getSidebar() {
        $sidebar = $this->instance['sidebar'];

        global $wp_registered_sidebars;

        if(isset($wp_registered_sidebars[$sidebar]))
            return $wp_registered_sidebars[$sidebar];

        return null;
    }

    public function getWidthOptions() {
        $sidebar = $this->getSidebar();
        $sidebarColClass = null;
        $widthOptions = array();

        if($sidebar) {
            if($sidebar)
                if(isset($sidebar['col_class']))
                    $sidebarColClass = $sidebar['col_class'];

            if($sidebarColClass) {
                if (in_array($sidebarColClass, array('col-md-12', 'col-md-8', 'col-md-4')))
                    $widthOptions['col-md-4'] = __('1/3 da Coluna', 'iasd');
                if (in_array($sidebarColClass, array('col-md-12', 'col-md-8')))
                    $widthOptions['col-md-8'] = __('2/3 da Coluna', 'iasd');
                if (in_array($sidebarColClass, array('col-md-12')))
                    $widthOptions['col-md-12'] = __('Coluna Inteira', 'iasd');
            }
        }

        return $widthOptions;
    }

    public function getRenderedWidth() {
        $sidebar = $this->getSidebar();
        $sidebarColClass = null;
        $widthClass = $current = $this->getCurrentWidth();

        if($sidebar) {
            if($sidebar)
                if(isset($sidebar['col_class']))
                    $sidebarColClass = $sidebar['col_class'];

            if($sidebarColClass == 'col-md-8') {
                $widthClass = ($current == 'col-md-4') ? 'col-md-6' : 'col-md-12';
            } else if($sidebarColClass == 'col-md-4') {
                $widthClass = 'col-md-4';
            }
        }

        return $widthClass;
    }

    public function getCurrentWidth() {
        return $this->instance['width'];
    }

    public function renderWidthOptions() {
        $widthOptions = $this->getWidthOptions();
        $current = $this->getCurrentWidth();

        if(count($widthOptions)) {

?>
<p>
    <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Largura:', 'iasd'); ?></label>
    <select class="widefat " id="<?php echo $this->get_field_id('width'); ?>"
            name="<?php echo $this->get_field_name('width'); ?>" >
        <?php foreach($widthOptions as $col_id => $desc) echo '<option value="', $col_id,'"', ($current == $col_id) ? ' selected="selected" ' : '', '>', $desc, '</option>';?>
    </select>
</p>
<?php
        }
    }


    public function form($instance) {
        $this->setInstance($instance);
        if(isset($instance['sidebar']) && $instance['sidebar']) {
            $this->subForm($instance);
        } else {
            echo '<p class="no-options-widget adaptablewidget">' . __('Aguarde o carregamento das opções iniciais', 'iasd') . '</p>';
        }
    }
    public function subForm($instance) {}


    public function update( $new_instance, $old_instance ) {
        if(!isset($new_instance['sidebar']) || !$new_instance['sidebar']) {
            if(isset($old_instance['sidebar']) && $old_instance['sidebar'])
                $new_instance['sidebar'] = $old_instance['sidebar'];
            else if(isset($_REQUEST['sidebar']) && $_REQUEST['sidebar'])
                $new_instance['sidebar'] = $_REQUEST['sidebar'];
        }

        return $this->subUpdate($new_instance, $old_instance);
    }
    public function subUpdate( $new_instance, $old_instance ) { return $new_instance; }


    public function widget($args, $instance) {
        $this->setInstance($instance);

        $this->subWidget($args, $instance);
    }
    public function subWidget($args, $instance) { }
}


