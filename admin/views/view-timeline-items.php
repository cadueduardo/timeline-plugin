<?php
/**
 * View for the Timeline Items Tab
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// As variáveis ($items) são passadas do escopo de Timeline_Admin::admin_page().
?>
<form method="post" action="">
    <?php wp_nonce_field('timeline_save', 'timeline_nonce'); ?>
    
    <h3>Adicionar/Editar Itens da Timeline</h3>
    <div id="timeline-items-container">
        <?php if (empty($items)): ?>
            <div class="timeline-item-form">
                <h4>Item 1</h4>
                <table class="form-table">
                    <tr><th scope="row">Ano</th><td><input type="text" name="timeline_items[0][year]" value="" class="regular-text" /></td></tr>
                    <tr><th scope="row">Título</th><td><input type="text" name="timeline_items[0][title]" value="" class="regular-text" /></td></tr>
                    <tr><th scope="row">Descrição</th><td><textarea name="timeline_items[0][description]" rows="3" class="large-text"></textarea></td></tr>
                    <tr><th scope="row">Imagem</th><td><input type="url" name="timeline_items[0][image]" value="" class="regular-text image-url" /><button type="button" class="button upload-image-button">Selecionar da Galeria</button><div class="image-preview"></div></td></tr>
                    <tr><th scope="row">Texto Lateral</th><td><input type="text" name="timeline_items[0][data_text]" value="" class="regular-text" /></td></tr>
                </table>
                <button type="button" class="button remove-item">Remover Item</button><hr>
            </div>
        <?php else: ?>
            <?php foreach ($items as $index => $item): ?>
                <div class="timeline-item-form">
                    <h4>Item <?php echo $index + 1; ?></h4>
                    <table class="form-table">
                        <tr><th scope="row">Ano</th><td><input type="text" name="timeline_items[<?php echo $index; ?>][year]" value="<?php echo esc_attr($item['year']); ?>" class="regular-text" /></td></tr>
                        <tr><th scope="row">Título</th><td><input type="text" name="timeline_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="regular-text" /></td></tr>
                        <tr><th scope="row">Descrição</th><td><textarea name="timeline_items[<?php echo $index; ?>][description]" rows="3" class="large-text"><?php echo esc_textarea($item['description']); ?></textarea></td></tr>
                        <tr><th scope="row">Imagem</th><td><input type="url" name="timeline_items[<?php echo $index; ?>][image]" value="<?php echo esc_url($item['image']); ?>" class="regular-text image-url" /><button type="button" class="button upload-image-button">Selecionar da Galeria</button><div class="image-preview"><?php if (!empty($item['image'])): ?><img src="<?php echo esc_url($item['image']); ?>" /><?php endif; ?></div></td></tr>
                        <tr><th scope="row">Texto Lateral</th><td><input type="text" name="timeline_items[<?php echo $index; ?>][data_text]" value="<?php echo esc_attr($item['data_text']); ?>" class="regular-text" /></td></tr>
                    </table>
                    <button type="button" class="button remove-item">Remover Item</button><hr>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <button type="button" id="add-item" class="button">Adicionar Novo Item</button>
    <br><br>
    <?php submit_button('Salvar Timeline'); ?>
</form> 