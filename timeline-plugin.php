<?php
/**
 * Plugin Name: Timeline Interativa
 * Plugin URI: https://github.com/cadueduardo
 * Description: Timeline dinâmica baseado nos códigos do Mert Cukuren (@knyttneve) do site: https://codepen.io/knyttneve/pen/bgvmma/
 * Version: 1.3.5
 * Author: Carlos Eduardo
 * Author URI: https://github.com/cadueduardo
 * License: GPL v2 or later
 */

// Impedir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes do plugin
define('TIMELINE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TIMELINE_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Carregar dependências
require_once TIMELINE_PLUGIN_PATH . 'admin/class-timeline-admin.php';
require_once TIMELINE_PLUGIN_PATH . 'includes/class-github-updater.php';

class TimelinePlugin {
    
    public function __construct() {
        if (is_admin()) {
            new Timeline_Admin(__FILE__);
        }
        
        // Registrar hooks do frontend
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('timeline', array($this, 'timeline_shortcode'));
    }
    
    /**
     * Carregar CSS e JS do frontend
     */
    public function enqueue_scripts() {
        wp_enqueue_style('timeline-style', TIMELINE_PLUGIN_URL . 'assets/timeline.css', array(), '1.3.5');
        wp_enqueue_script('timeline-script', TIMELINE_PLUGIN_URL . 'assets/timeline.js', array('jquery'), '1.3.5', true);
        
        // Enfileirar fontes do Google selecionadas
        $this->enqueue_google_fonts();
    }
    
    /**
     * Criar shortcode [timeline]
     */
    public function timeline_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 'timeline-1',
            'title' => 'Minha Timeline',
            'subtitle' => ''
        ), $atts);
        
        $bg_type = get_option('timeline_background_type', 'dynamic');
        $bg_image = get_option('timeline_background_image', '');
        $bg_color = get_option('timeline_background_color', '#333333');
        $overlay_opacity = get_option('timeline_overlay_opacity', '0.8');
        
        $bg_style = '';
        $bg_class = '';
        
        switch ($bg_type) {
            case 'fixed_image':
                if ($bg_image) $bg_style = 'background-image: url(' . esc_url($bg_image) . ');';
                break;
            case 'solid_color':
                $bg_style = 'background-color: ' . esc_attr($bg_color) . ';';
                break;
            case 'none':
                $bg_class = 'no-background';
                break;
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr($atts['id']); ?>" class="timeline-container <?php echo $bg_class; ?>" style="<?php echo $bg_style; ?>">
            <?php if ($bg_type !== 'none'): ?>
                <div class="timeline-overlay" style="background: rgba(99, 99, 99, <?php echo esc_attr($overlay_opacity); ?>);"></div>
            <?php endif; ?>
            <div class="timeline-content-wrapper">
                <div class="timeline-header">
                    <h2 class="timeline-header__title"><?php echo esc_html($atts['title']); ?></h2>
                    <?php if ($atts['subtitle']): ?>
                        <h3 class="timeline-header__subtitle"><?php echo esc_html($atts['subtitle']); ?></h3>
                    <?php endif; ?>
                </div>
                <div class="timeline">
                    <?php $this->render_timeline_items(); ?>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            <?php if ($bg_type === 'dynamic'): ?>
                $("#<?php echo esc_js($atts['id']); ?>").timeline();
            <?php else: ?>
                $("#<?php echo esc_js($atts['id']); ?>").timelineStatic();
            <?php endif; ?>
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enfileirar fontes do Google selecionadas para o frontend
     */
    public function enqueue_google_fonts() {
        $customization = get_option('timeline_customization', array());
        $fields = ['year', 'description', 'data_text'];
        $font_families = [];

        foreach ($fields as $field) {
            $font_family = $customization[$field . '_font_family'] ?? null;
            if ($font_family && $font_family !== 'default') {
                $font_weight = $customization[$field . '_font_weight'] ?? '400';
                $font_weight = str_replace('italic', '', $font_weight);
                $font_weight = ($font_weight === 'regular') ? '400' : $font_weight;
                $font_families[$font_family][] = $font_weight;
            }
        }

        if (empty($font_families)) return;

        $query_args = [];
        foreach ($font_families as $family => $weights) {
            $weights = array_unique($weights);
            $query_args[] = 'family=' . str_replace(' ', '+', $family) . ':ital,wght@0,' . implode(';0,', $weights) . ';1,' . implode(';1,', $weights);
        }

        $fonts_url = 'https://fonts.googleapis.com/css2?' . implode('&', $query_args) . '&display=swap';
        wp_enqueue_style('timeline-google-fonts', $fonts_url, array(), null);
    }

    /**
     * Renderizar itens da timeline com base nas configurações
     */
    private function render_timeline_items() {
        $items = get_option('timeline_items', array());
        $customization = get_option('timeline_customization', array());

        // Função auxiliar para gerar estilos e classes
        $get_element_attributes = function($element) use ($customization) {
            $style = '';
            $class = '';
            
            // Fonte
            $font_family = $customization[$element . '_font_family'] ?? 'default';
            if ($font_family !== 'default') {
                $style .= 'font-family: &quot;' . esc_attr($font_family) . '&quot;;';
            }

            // Peso da fonte
            $font_weight = $customization[$element . '_font_weight'] ?? 'regular';
             if ($font_weight !== 'regular') {
                $style .= 'font-weight: ' . esc_attr($font_weight) . ';';
            }

            // Tamanho da Fonte
            $font_size = $customization[$element . '_font_size'] ?? '';
            $font_size_unit = $customization[$element . '_font_size_unit'] ?? 'px';
            if (!empty($font_size)) {
                $style .= 'font-size: ' . esc_attr($font_size) . $font_size_unit . ';';
            }

            // Cor
            $color = $customization[$element . '_color'] ?? '';
            if (!empty($color)) {
                $style .= 'color: ' . esc_attr($color) . ';';
            }
            
            // Sombra de Texto
            $shadow = $customization[$element . '_shadow'] ?? '';
            if (!empty($shadow)) {
                $style .= 'text-shadow: ' . esc_attr($shadow) . ';';
            }

            // Classe customizada
            $custom_class = $customization[$element . '_class'] ?? '';
            if (!empty($custom_class)) {
                $class .= ' ' . esc_attr($custom_class);
            }
            
            return 'class="' . trim($class) . '" style="' . $style . '"';
        };

        if (empty($items)) {
            echo '<p>Nenhum item na timeline ainda.</p>';
            return;
        }

        foreach ($items as $index => $item) {
            $image_url = esc_url($item['image']);
            $year_attr = $get_element_attributes('year');
            $title_attr = $get_element_attributes('data_text'); // Usa a mesma customização do data-text
            $desc_attr = $get_element_attributes('description');

            $image_class = '';
            if (!empty($customization['image_border_radius']) && $customization['image_border_radius'] > 0) {
                $image_class .= ' has-border-radius';
            }
            if (!empty($customization['image_shadow'])) {
                $image_class .= ' has-shadow';
            }
            $image_style = 'border-radius:' . esc_attr($customization['image_border_radius'] ?? 0) . 'px;';

            ?>
            <div class="timeline-item" data-text="<?php echo esc_attr($item['title']); ?>">
                <div class="timeline__content">
                    <img class="timeline__img <?php echo $image_class; ?>" src="<?php echo $image_url; ?>" style="<?php echo $image_style; ?>"/>
                    <h2 class="timeline__content-title" <?php echo $title_attr; ?>><?php echo esc_html($item['title']); ?></h2>
                    <p class="timeline__content-desc" <?php echo $desc_attr; ?>><?php echo wp_kses_post($item['description']); ?></p>
                </div>
                <div class="timeline__year" <?php echo $year_attr; ?>>
                    <?php echo esc_html($item['year']); ?>
                </div>
            </div>
            <?php
        }
    }
}

/**
 * Inicializar o plugin
 */
function timeline_plugin_init() {
    new TimelinePlugin();
    
    if (is_admin()) {
        new GitHubUpdater(__FILE__, 'cadueduardo', 'timeline-plugin');
    }
}
add_action('plugins_loaded', 'timeline_plugin_init');
