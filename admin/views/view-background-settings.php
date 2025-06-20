<?php
/**
 * View for the Background Settings Tab
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// As variáveis ($bg_type, $bg_image, etc.) são passadas do escopo de Timeline_Admin::admin_page().
?>
<form method="post" action="">
    <?php wp_nonce_field('timeline_bg_save', 'timeline_bg_nonce'); ?>
    
    <h3>Configurações de Background</h3>
    <table class="form-table">
        <tr>
            <th scope="row">Tipo de Background</th>
            <td>
                <label><input type="radio" name="timeline_background_type" value="dynamic" <?php checked($bg_type, 'dynamic'); ?> /> <strong>Dinâmico (Original)</strong> - Muda conforme o scroll.</label><br><br>
                <label><input type="radio" name="timeline_background_type" value="fixed_image" <?php checked($bg_type, 'fixed_image'); ?> /> <strong>Imagem Fixa</strong> - Uma imagem de background personalizada.</label><br><br>
                <label><input type="radio" name="timeline_background_type" value="solid_color" <?php checked($bg_type, 'solid_color'); ?> /> <strong>Cor Sólida</strong> - Uma cor de fundo personalizada.</label><br><br>
                <label><input type="radio" name="timeline_background_type" value="none" <?php checked($bg_type, 'none'); ?> /> <strong>Sem Background</strong> - Não há background.</label>
            </td>
        </tr>
        <tr class="bg-image-row">
            <th scope="row">Imagem de Background</th>
            <td>
                <input type="url" name="timeline_background_image" value="<?php echo esc_url($bg_image); ?>" class="regular-text image-url" />
                <button type="button" class="button upload-bg-button">Selecionar da Galeria</button>
                <div class="bg-image-preview" style="margin-top: 10px;">
                    <?php if (!empty($bg_image)): ?>
                        <img src="<?php echo esc_url($bg_image); ?>" style="max-width: 300px; height: auto;" />
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr class="bg-color-row">
            <th scope="row">Cor de Background</th>
            <td>
                <input type="color" name="timeline_background_color" value="<?php echo esc_attr($bg_color); ?>" />
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