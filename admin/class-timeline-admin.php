<?php
/**
 * Admin Class for Timeline Plugin
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Timeline_Admin {

    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_init', array($this, 'handle_force_update_check'));
        add_action('admin_notices', array($this, 'show_update_checked_notice'));
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), array($this, 'add_settings_link'));
    }

    public function admin_init() {
        register_setting('timeline_settings', 'timeline_items');
        register_setting('timeline_settings', 'timeline_background_type');
        register_setting('timeline_settings', 'timeline_background_image');
        register_setting('timeline_settings', 'timeline_background_color');
        register_setting('timeline_settings', 'timeline_overlay_opacity');
        register_setting('timeline_settings', 'timeline_customization');
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=timeline-settings') . '">Configurações</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function admin_menu() {
        add_options_page(
            'Timeline Settings',
            'Timeline',
            'manage_options',
            'timeline-settings',
            array($this, 'admin_page')
        );
    }

    public function admin_page() {
        $this->process_form_submissions();

        // Carregar dados para as views
        $items = get_option('timeline_items', array());
        $bg_type = get_option('timeline_background_type', 'dynamic');
        $bg_image = get_option('timeline_background_image', '');
        $bg_color = get_option('timeline_background_color', '#333333');
        $overlay_opacity = get_option('timeline_overlay_opacity', '0.8');
        $customization = get_option('timeline_customization', array());
        
        $google_fonts = [];
        $font_file = TIMELINE_PLUGIN_PATH . 'assets/google-fonts.json';
        if (file_exists($font_file)) {
            $google_fonts = json_decode(file_get_contents($font_file), true);
        }

        require_once TIMELINE_PLUGIN_PATH . 'admin/views/view-admin-page.php';
    }

    private function process_form_submissions() {
        // Processar formulário de itens
        if (isset($_POST['submit']) && isset($_POST['timeline_nonce']) && wp_verify_nonce($_POST['timeline_nonce'], 'timeline_save')) {
            $items = array();
            if (isset($_POST['timeline_items']) && is_array($_POST['timeline_items'])) {
                foreach ($_POST['timeline_items'] as $item) {
                    if (!empty($item['year']) && !empty($item['title'])) {
                        $items[] = array(
                            'year' => sanitize_text_field($item['year']),
                            'title' => sanitize_text_field($item['title']),
                            'description' => sanitize_textarea_field($item['description']),
                            'image' => esc_url_raw($item['image']),
                            'data_text' => sanitize_text_field($item['data_text'])
                        );
                    }
                }
            }
            update_option('timeline_items', $items);
            echo '<div class="notice notice-success"><p>Timeline atualizada com sucesso!</p></div>';
        }
        
        // Processar formulário de background
        if (isset($_POST['submit_background']) && isset($_POST['timeline_bg_nonce']) && wp_verify_nonce($_POST['timeline_bg_nonce'], 'timeline_bg_save')) {
            update_option('timeline_background_type', sanitize_text_field($_POST['timeline_background_type']));
            update_option('timeline_background_image', esc_url_raw($_POST['timeline_background_image']));
            update_option('timeline_background_color', sanitize_hex_color($_POST['timeline_background_color']));
            update_option('timeline_overlay_opacity', floatval($_POST['timeline_overlay_opacity']));
            echo '<div class="notice notice-success"><p>Configurações de background atualizadas com sucesso!</p></div>';
        }
        
        // Processar formulário de customização
        if (isset($_POST['submit_customization']) && isset($_POST['timeline_customization_nonce']) && wp_verify_nonce($_POST['timeline_customization_nonce'], 'timeline_customization_save')) {
            $customization_data = isset($_POST['timeline_customization']) ? (array) $_POST['timeline_customization'] : array();
            // Sanitização simples (expandir se necessário)
            foreach ($customization_data as $key => $value) {
                if (is_string($value)) {
                    $customization_data[$key] = sanitize_text_field($value);
                }
            }
            update_option('timeline_customization', $customization_data);
            echo '<div class="notice notice-success"><p>Configurações de customização salvas!</p></div>';
        }
    }

    public function handle_force_update_check() {
        if (
            current_user_can('update_plugins') &&
            isset($_GET['page']) && $_GET['page'] === 'timeline-plugin' && 
            isset($_GET['force-check-update']) && $_GET['force-check-update'] === 'true'
        ) {
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'timeline_force_check')) {
                wp_die('Ação inválida detectada.', 'Erro de segurança');
            }

            // Força a limpeza do cache de atualizações
            delete_site_transient('update_plugins');
            
            // Redireciona de volta com uma mensagem de sucesso
            wp_safe_redirect(admin_url('admin.php?page=timeline-plugin&tab=usage&update-checked=true'));
            exit;
        }
    }

    public function show_update_checked_notice() {
        if (
            current_user_can('update_plugins') &&
            isset($_GET['page']) && $_GET['page'] === 'timeline-plugin' && 
            isset($_GET['update-checked']) && $_GET['update-checked'] === 'true'
        ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Cache de atualizações limpo.</strong> O WordPress agora irá buscar por novas versões do plugin. A notificação de atualização pode levar um ou dois minutos para aparecer.</p>
            </div>
            <?php
        }
    }
} 