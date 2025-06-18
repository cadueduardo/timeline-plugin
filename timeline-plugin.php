<?php
/**
 * Plugin Name: Timeline Interativa
 * Plugin URI: https://github.com/cadueduardo
 * Description: Timeline dinâmica baseado nos códigos do Mert Cukuren (@knyttneve) do site: https://codepen.io/knyttneve/pen/bgvmma/
 * Version: 1.0.0
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

class TimelinePlugin {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('timeline', array($this, 'timeline_shortcode'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        
        // Adicionar link de configurações na lista de plugins
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
    }
    
    // Carregar CSS e JS
    public function enqueue_scripts() {
        wp_enqueue_style('timeline-style', TIMELINE_PLUGIN_URL . 'assets/timeline.css', array(), '1.0.0');
        wp_enqueue_script('timeline-script', TIMELINE_PLUGIN_URL . 'assets/timeline.js', array('jquery'), '1.0.0', true);
        
        // Carregar media library no admin
        if (is_admin()) {
            wp_enqueue_media();
        }
    }
    
    // Criar shortcode [timeline]
    public function timeline_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 'timeline-1',
            'title' => 'Minha Timeline',
            'subtitle' => ''
        ), $atts);
        
        // Configurações de background
        $bg_type = get_option('timeline_background_type', 'dynamic');
        $bg_image = get_option('timeline_background_image', '');
        $bg_color = get_option('timeline_background_color', '#333333');
        $overlay_opacity = get_option('timeline_overlay_opacity', '0.8');
        
        $bg_style = '';
        $use_dynamic_bg = true;
        
        switch ($bg_type) {
            case 'fixed_image':
                if ($bg_image) {
                    $bg_style = 'background-image: url(' . esc_url($bg_image) . ');';
                    $use_dynamic_bg = false;
                }
                break;
            case 'solid_color':
                $bg_style = 'background-color: ' . esc_attr($bg_color) . ';';
                $use_dynamic_bg = false;
                break;
            case 'dynamic':
            default:
                $use_dynamic_bg = true;
                break;
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr($atts['id']); ?>" class="timeline-container" style="<?php echo $bg_style; ?>">
            <div class="timeline-overlay" style="background: rgba(99, 99, 99, <?php echo esc_attr($overlay_opacity); ?>);"></div>
            <div class="timeline-content-wrapper">
                <div class="timeline-header">
                    <h2 class="timeline-header__title"><?php echo esc_html($atts['title']); ?></h2>
                    <?php if ($atts['subtitle']): ?>
                        <h3 class="timeline-header__subtitle"><?php echo esc_html($atts['subtitle']); ?></h3>
                    <?php endif; ?>
                </div>
                <div class="timeline">
                    <?php
                    // Aqui você pode adicionar itens da timeline dinamicamente
                    // Por enquanto, vou usar um exemplo
                    $this->render_timeline_items();
                    ?>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            <?php if ($use_dynamic_bg): ?>
                $("#<?php echo esc_js($atts['id']); ?>").timeline();
            <?php else: ?>
                // Aplicar apenas as animações, sem mudança de background
                $("#<?php echo esc_js($atts['id']); ?>").timelineStatic();
            <?php endif; ?>
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    // Renderizar itens da timeline
    private function render_timeline_items() {
        // Buscar itens salvos no banco de dados
        $items = get_option('timeline_items', array());
        
        // Se não houver itens salvos, usar exemplos padrão
        if (empty($items)) {
            $items = array(
                array(
                    'year' => '2020',
                    'title' => 'Início do Projeto',
                    'description' => 'Descrição do que aconteceu em 2020...',
                    'image' => 'https://via.placeholder.com/400x300',
                    'data_text' => 'PROJETO'
                ),
                array(
                    'year' => '2021',
                    'title' => 'Desenvolvimento',
                    'description' => 'Fase de desenvolvimento e criação...',
                    'image' => 'https://via.placeholder.com/400x300',
                    'data_text' => 'DESENVOLVIMENTO'
                ),
                array(
                    'year' => '2022',
                    'title' => 'Lançamento',
                    'description' => 'Lançamento oficial do projeto...',
                    'image' => 'https://via.placeholder.com/400x300',
                    'data_text' => 'LANÇAMENTO'
                )
            );
        }
        
        foreach ($items as $item): ?>
            <div class="timeline-item" data-text="<?php echo esc_attr($item['data_text']); ?>">
                <div class="timeline__content">
                    <img class="timeline__img" src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                    <h2 class="timeline__content-title"><?php echo esc_html($item['year']); ?></h2>
                    <p class="timeline__content-desc"><?php echo esc_html($item['description']); ?></p>
                </div>
            </div>
        <?php endforeach;
    }
    
    // Inicializar configurações administrativas
    public function admin_init() {
        register_setting('timeline_settings', 'timeline_items');
        register_setting('timeline_settings', 'timeline_background_type');
        register_setting('timeline_settings', 'timeline_background_image');
        register_setting('timeline_settings', 'timeline_background_color');
        register_setting('timeline_settings', 'timeline_overlay_opacity');
    }
    
    // Adicionar link de configurações na lista de plugins
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=timeline-settings') . '">Configurações</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    // Menu administrativo
    public function admin_menu() {
        add_options_page(
            'Timeline Settings',
            'Timeline',
            'manage_options',
            'timeline-settings',
            array($this, 'admin_page')
        );
    }
    
    // Página administrativa
    public function admin_page() {
        // Processar formulário de itens
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['timeline_nonce'], 'timeline_save')) {
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
        if (isset($_POST['submit_background']) && wp_verify_nonce($_POST['timeline_bg_nonce'], 'timeline_bg_save')) {
            update_option('timeline_background_type', sanitize_text_field($_POST['timeline_background_type']));
            update_option('timeline_background_image', esc_url_raw($_POST['timeline_background_image']));
            update_option('timeline_background_color', sanitize_hex_color($_POST['timeline_background_color']));
            update_option('timeline_overlay_opacity', floatval($_POST['timeline_overlay_opacity']));
            echo '<div class="notice notice-success"><p>Configurações de background atualizadas com sucesso!</p></div>';
        }
        
        $items = get_option('timeline_items', array());
        $bg_type = get_option('timeline_background_type', 'dynamic');
        $bg_image = get_option('timeline_background_image', '');
        $bg_color = get_option('timeline_background_color', '#333333');
        $overlay_opacity = get_option('timeline_overlay_opacity', '0.8');
        ?>
        <div class="wrap">
            <h1>Configurações da Timeline</h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="#timeline-items" class="nav-tab nav-tab-active">Itens da Timeline</a>
                <a href="#timeline-background" class="nav-tab">Background</a>
                <a href="#timeline-usage" class="nav-tab">Como Usar</a>
            </h2>
            
            <div id="timeline-items" class="tab-content">
                <form method="post" action="">
                    <?php wp_nonce_field('timeline_save', 'timeline_nonce'); ?>
                    
                    <h3>Adicionar/Editar Itens da Timeline</h3>
                    <div id="timeline-items-container">
                        <?php if (empty($items)): ?>
                            <div class="timeline-item-form">
                                <h4>Item 1</h4>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Ano</th>
                                        <td><input type="text" name="timeline_items[0][year]" value="" class="regular-text" /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Título</th>
                                        <td><input type="text" name="timeline_items[0][title]" value="" class="regular-text" /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Descrição</th>
                                        <td><textarea name="timeline_items[0][description]" rows="3" class="large-text"></textarea></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Imagem</th>
                                        <td>
                                            <input type="url" name="timeline_items[0][image]" value="" class="regular-text image-url" />
                                            <button type="button" class="button upload-image-button">Selecionar da Galeria</button>
                                            <div class="image-preview" style="margin-top: 10px;"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Texto Lateral</th>
                                        <td><input type="text" name="timeline_items[0][data_text]" value="" class="regular-text" /></td>
                                    </tr>
                                </table>
                                <button type="button" class="button remove-item">Remover Item</button>
                                <hr>
                            </div>
                        <?php else: ?>
                            <?php foreach ($items as $index => $item): ?>
                                <div class="timeline-item-form">
                                    <h4>Item <?php echo $index + 1; ?></h4>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">Ano</th>
                                            <td><input type="text" name="timeline_items[<?php echo $index; ?>][year]" value="<?php echo esc_attr($item['year']); ?>" class="regular-text" /></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Título</th>
                                            <td><input type="text" name="timeline_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="regular-text" /></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Descrição</th>
                                            <td><textarea name="timeline_items[<?php echo $index; ?>][description]" rows="3" class="large-text"><?php echo esc_textarea($item['description']); ?></textarea></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Imagem</th>
                                            <td>
                                                <input type="url" name="timeline_items[<?php echo $index; ?>][image]" value="<?php echo esc_url($item['image']); ?>" class="regular-text image-url" />
                                                <button type="button" class="button upload-image-button">Selecionar da Galeria</button>
                                                <div class="image-preview" style="margin-top: 10px;">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="<?php echo esc_url($item['image']); ?>" style="max-width: 150px; height: auto;" />
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Texto Lateral</th>
                                            <td><input type="text" name="timeline_items[<?php echo $index; ?>][data_text]" value="<?php echo esc_attr($item['data_text']); ?>" class="regular-text" /></td>
                                        </tr>
                                    </table>
                                    <button type="button" class="button remove-item">Remover Item</button>
                                    <hr>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" id="add-item" class="button">Adicionar Novo Item</button>
                    <br><br>
                    <?php submit_button('Salvar Timeline'); ?>
                </form>
            </div>
            
            <div id="timeline-background" class="tab-content" style="display: none;">
                <form method="post" action="">
                    <?php wp_nonce_field('timeline_bg_save', 'timeline_bg_nonce'); ?>
                    
                    <h3>Configurações de Background</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Tipo de Background</th>
                            <td>
                                <label>
                                    <input type="radio" name="timeline_background_type" value="dynamic" <?php checked($bg_type, 'dynamic'); ?> />
                                    <strong>Dinâmico (Original)</strong> - Muda conforme o scroll das imagens dos itens
                                </label><br><br>
                                <label>
                                    <input type="radio" name="timeline_background_type" value="fixed_image" <?php checked($bg_type, 'fixed_image'); ?> />
                                    <strong>Imagem Fixa</strong> - Uma imagem de background personalizada
                                </label><br><br>
                                <label>
                                    <input type="radio" name="timeline_background_type" value="solid_color" <?php checked($bg_type, 'solid_color'); ?> />
                                    <strong>Cor Sólida</strong> - Uma cor de fundo personalizada
                                </label>
                            </td>
                        </tr>
                        <tr class="bg-image-row" <?php if ($bg_type !== 'fixed_image') echo 'style="display: none;"'; ?>>
                            <th scope="row">Imagem de Background</th>
                            <td>
                                <input type="url" name="timeline_background_image" value="<?php echo esc_url($bg_image); ?>" class="regular-text image-url" />
                                <button type="button" class="button upload-bg-button">Selecionar da Galeria</button>
                                <div class="bg-image-preview" style="margin-top: 10px;">
                                    <?php if (!empty($bg_image)): ?>
                                        <img src="<?php echo esc_url($bg_image); ?>" style="max-width: 300px; height: auto;" />
                                    <?php endif; ?>
                                </div>
                                <p class="description">Escolha uma imagem que será usada como background fixo da timeline.</p>
                            </td>
                        </tr>
                        <tr class="bg-color-row" <?php if ($bg_type !== 'solid_color') echo 'style="display: none;"'; ?>>
                            <th scope="row">Cor de Background</th>
                            <td>
                                <input type="color" name="timeline_background_color" value="<?php echo esc_attr($bg_color); ?>" />
                                <p class="description">Escolha uma cor sólida para o background da timeline.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Opacidade do Overlay</th>
                            <td>
                                <input type="range" name="timeline_overlay_opacity" value="<?php echo esc_attr($overlay_opacity); ?>" min="0" max="1" step="0.1" class="opacity-slider" />
                                <span class="opacity-value"><?php echo esc_attr($overlay_opacity); ?></span>
                                <p class="description">Controla a transparência da camada escura sobre o background (0 = transparente, 1 = opaco).</p>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button('Salvar Configurações de Background', 'primary', 'submit_background'); ?>
                </form>
            </div>
            
            <div id="timeline-usage" class="tab-content" style="display: none;">
                <h3>Como usar a Timeline</h3>
                <p>Use o shortcode <code>[timeline title="Seu Título" subtitle="Seu Subtítulo"]</code> para inserir a timeline em qualquer página ou post.</p>
                
                <h4>Exemplo de uso:</h4>
                <pre>[timeline title="Minha História" subtitle="Uma jornada incrível"]</pre>
                
                <h4>Parâmetros disponíveis:</h4>
                <ul>
                    <li><strong>title</strong>: Título principal da timeline</li>
                    <li><strong>subtitle</strong>: Subtítulo (opcional)</li>
                    <li><strong>id</strong>: ID único para múltiplas timelines na mesma página</li>
                </ul>
                
                <h4>Tipos de Background:</h4>
                <ul>
                    <li><strong>Dinâmico</strong>: Background muda automaticamente com as imagens dos itens (funcionalidade original)</li>
                    <li><strong>Imagem Fixa</strong>: Uma imagem personalizada como background</li>
                    <li><strong>Cor Sólida</strong>: Uma cor de fundo personalizada</li>
                </ul>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            var itemCount = <?php echo count($items); ?>;
            
            $('#add-item').click(function() {
                var newItem = `
                    <div class="timeline-item-form">
                        <h4>Item ${itemCount + 1}</h4>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Ano</th>
                                <td><input type="text" name="timeline_items[${itemCount}][year]" value="" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th scope="row">Título</th>
                                <td><input type="text" name="timeline_items[${itemCount}][title]" value="" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th scope="row">Descrição</th>
                                <td><textarea name="timeline_items[${itemCount}][description]" rows="3" class="large-text"></textarea></td>
                            </tr>
                                                                         <tr>
                                                 <th scope="row">Imagem</th>
                                                 <td>
                                                     <input type="url" name="timeline_items[${itemCount}][image]" value="" class="regular-text image-url" />
                                                     <button type="button" class="button upload-image-button">Selecionar da Galeria</button>
                                                     <div class="image-preview" style="margin-top: 10px;"></div>
                                                 </td>
                                             </tr>
                            <tr>
                                <th scope="row">Texto Lateral</th>
                                <td><input type="text" name="timeline_items[${itemCount}][data_text]" value="" class="regular-text" /></td>
                            </tr>
                        </table>
                        <button type="button" class="button remove-item">Remover Item</button>
                        <hr>
                    </div>
                `;
                $('#timeline-items-container').append(newItem);
                itemCount++;
            });
            
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.timeline-item-form').remove();
            });
            
            // Funcionalidade da galeria de mídia para itens
            $(document).on('click', '.upload-image-button', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var imageInput = button.siblings('.image-url');
                var imagePreview = button.siblings('.image-preview');
                
                var mediaUploader = wp.media({
                    title: 'Selecionar Imagem para Timeline',
                    button: {
                        text: 'Usar esta imagem'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    imageInput.val(attachment.url);
                    imagePreview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />');
                });
                
                mediaUploader.open();
            });
            
            // Funcionalidade da galeria de mídia para background
            $(document).on('click', '.upload-bg-button', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var imageInput = button.siblings('.image-url');
                var imagePreview = button.siblings('.bg-image-preview');
                
                var mediaUploader = wp.media({
                    title: 'Selecionar Imagem de Background',
                    button: {
                        text: 'Usar esta imagem'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    imageInput.val(attachment.url);
                    imagePreview.html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto;" />');
                });
                
                mediaUploader.open();
            });
            
            // Controle de visibilidade das opções de background
            $('input[name="timeline_background_type"]').change(function() {
                var selectedType = $(this).val();
                $('.bg-image-row, .bg-color-row').hide();
                
                if (selectedType === 'fixed_image') {
                    $('.bg-image-row').show();
                } else if (selectedType === 'solid_color') {
                    $('.bg-color-row').show();
                }
            });
            
            // Atualização do valor da opacidade em tempo real
            $('.opacity-slider').on('input', function() {
                $('.opacity-value').text($(this).val());
            });
            
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').hide();
                $($(this).attr('href')).show();
            });
        });
        </script>
        
        <style>
        .timeline-item-form {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #0073aa;
        }
        .tab-content {
            margin-top: 20px;
        }
        .upload-image-button {
            margin-left: 10px;
            vertical-align: top;
        }
        .image-preview img {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
            max-width: 150px;
            height: auto;
        }
        .image-url {
            width: 300px;
        }
        </style>
        <?php
    }
}

// Inicializar o plugin
new TimelinePlugin();

// Sistema de atualização via GitHub
if (!class_exists('GitHubUpdater')) {
    class GitHubUpdater {
        
        private $plugin_file;
        private $plugin_slug;
        private $version;
        private $github_username;
        private $github_repo;
        
        public function __construct($plugin_file, $github_username, $github_repo) {
            $this->plugin_file = $plugin_file;
            $this->plugin_slug = plugin_basename($plugin_file);
            $this->version = get_plugin_data($plugin_file)['Version'];
            $this->github_username = $github_username;
            $this->github_repo = $github_repo;
            
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
            add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        }
        
        public function check_for_update($transient) {
            if (empty($transient->checked)) {
                return $transient;
            }
            
            $latest_release = $this->get_latest_release();
            
            if ($latest_release && version_compare($this->version, ltrim($latest_release['tag_name'], 'v'), '<')) {
                $transient->response[$this->plugin_slug] = (object) array(
                    'slug' => dirname($this->plugin_slug),
                    'plugin' => $this->plugin_slug,
                    'new_version' => ltrim($latest_release['tag_name'], 'v'),
                    'url' => $latest_release['html_url'],
                    'package' => $latest_release['zipball_url']
                );
            }
            
            return $transient;
        }
        
        public function plugin_info($res, $action, $args) {
            if ($action !== 'plugin_information') {
                return $res;
            }
            
            if ($args->slug !== dirname($this->plugin_slug)) {
                return $res;
            }
            
            $latest_release = $this->get_latest_release();
            
            if ($latest_release) {
                $res = (object) array(
                    'name' => 'Timeline Interativa',
                    'slug' => dirname($this->plugin_slug),
                    'version' => ltrim($latest_release['tag_name'], 'v'),
                    'author' => 'Carlos Eduardo',
                    'homepage' => "https://github.com/{$this->github_username}/{$this->github_repo}",
                    'sections' => array(
                        'description' => 'Timeline dinâmica baseado nos códigos do Mert Cukuren (@knyttneve)',
                        'changelog' => $latest_release['body'] ?: 'Atualização disponível'
                    ),
                    'download_link' => $latest_release['zipball_url']
                );
            }
            
            return $res;
        }
        
        private function get_latest_release() {
            $api_url = "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases/latest";
            
            $request = wp_remote_get($api_url, array(
                'timeout' => 10,
                'headers' => array(
                    'User-Agent' => 'Timeline-Plugin-Updater'
                )
            ));
            
            if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
                return json_decode(wp_remote_retrieve_body($request), true);
            }
            
            return false;
        }
    }
}

// Ativar sistema de atualização via GitHub
$github_updater = new GitHubUpdater(
    __FILE__, 
    'cadueduardo',           // Seu username GitHub
    'timeline-plugin'        // Nome do repositório (mude se necessário)
);