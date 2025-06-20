<?php
/**
 * Main Admin Page View
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// As variáveis ($items, $bg_type, etc.) são passadas do escopo de Timeline_Admin::admin_page().
?>
<div class="wrap">
    <h1>Configurações da Timeline</h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="#timeline-items" class="nav-tab nav-tab-active">Itens da Timeline</a>
        <a href="#timeline-background" class="nav-tab">Background</a>
        <a href="#timeline-customization" class="nav-tab">Customização Global</a>
        <a href="#timeline-usage" class="nav-tab">Como Usar</a>
    </h2>
    
    <div id="timeline-items" class="tab-content">
        <?php
        if (file_exists(TIMELINE_PLUGIN_PATH . 'admin/views/view-timeline-items.php')) {
            require TIMELINE_PLUGIN_PATH . 'admin/views/view-timeline-items.php';
        }
        ?>
    </div>
    
    <div id="timeline-background" class="tab-content" style="display: none;">
        <?php
        if (file_exists(TIMELINE_PLUGIN_PATH . 'admin/views/view-background-settings.php')) {
            require TIMELINE_PLUGIN_PATH . 'admin/views/view-background-settings.php';
        }
        ?>
    </div>

    <div id="timeline-customization" class="tab-content" style="display: none;">
        <?php
        if (file_exists(TIMELINE_PLUGIN_PATH . 'admin/views/view-global-customization.php')) {
            require TIMELINE_PLUGIN_PATH . 'admin/views/view-global-customization.php';
        }
        ?>
    </div>
    
    <div id="timeline-usage" class="tab-content" style="display: none;">
         <?php
        if (file_exists(TIMELINE_PLUGIN_PATH . 'admin/views/view-usage.php')) {
            require TIMELINE_PLUGIN_PATH . 'admin/views/view-usage.php';
        }
        ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Lógica das abas
    $('.nav-tab').click(function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });

    // Restaurar aba ativa
    var hash = window.location.hash;
    if (hash) {
        $('.nav-tab[href="' + hash + '"]').click();
    }

    // Lógica da galeria de mídia para itens e background (pode ser movida para um .js separado)
    var mediaUploader;

    $(document).on('click', '.upload-image-button, .upload-bg-button', function(e) {
        e.preventDefault();
        var button = $(this);
        
        mediaUploader = wp.media({
            title: 'Selecionar Imagem',
            button: { text: 'Usar esta imagem' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            var imageInput = button.siblings('.image-url');
            var imagePreview = button.siblings('.image-preview, .bg-image-preview');
            imageInput.val(attachment.url);
            imagePreview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />');
        });

        mediaUploader.open();
    });

    // Adicionar/Remover itens
    var itemCount = $('#timeline-items-container .timeline-item-form').length;
    $('#add-item').click(function() {
        itemCount++;
        var newItemHtml = `
            <div class="timeline-item-form">
                <h4>Item ${itemCount}</h4>
                <table class="form-table">
                    <tr><th scope="row">Ano</th><td><input type="text" name="timeline_items[${itemCount-1}][year]" value="" class="regular-text" /></td></tr>
                    <tr><th scope="row">Título</th><td><input type="text" name="timeline_items[${itemCount-1}][title]" value="" class="regular-text" /></td></tr>
                    <tr><th scope="row">Descrição</th><td><textarea name="timeline_items[${itemCount-1}][description]" rows="3" class="large-text"></textarea></td></tr>
                    <tr><th scope="row">Imagem</th><td><input type="url" name="timeline_items[${itemCount-1}][image]" value="" class="regular-text image-url" /><button type="button" class="button upload-image-button">Selecionar da Galeria</button><div class="image-preview"></div></td></tr>
                    <tr><th scope="row">Texto Lateral</th><td><input type="text" name="timeline_items[${itemCount-1}][data_text]" value="" class="regular-text" /></td></tr>
                </table>
                <button type="button" class="button remove-item">Remover Item</button><hr>
            </div>`;
        $('#timeline-items-container').append(newItemHtml);
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('.timeline-item-form').remove();
    });

    // Controle de visibilidade das opções de background
    $('input[name="timeline_background_type"]').change(function() {
        var selectedType = $(this).val();
        $('.bg-image-row, .bg-color-row').hide();
        if (selectedType === 'fixed_image') $('.bg-image-row').show();
        else if (selectedType === 'solid_color') $('.bg-color-row').show();
    }).change();

    // Slider de opacidade
    $('.opacity-slider').on('input', function() {
        $(this).siblings('.opacity-value').text($(this).val());
    });
});
</script>

<style>
.timeline-item-form, .timeline-customization-section {
    background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-left: 4px solid #0073aa;
}
.font-control-group {
    display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 10px;
}
.font-control-group .small-text { width: 80px; }
.tab-content { margin-top: 20px; }
.image-preview img, .bg-image-preview img { border: 1px solid #ddd; padding: 4px; max-width: 150px; height: auto; }
</style> 