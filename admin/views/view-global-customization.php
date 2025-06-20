<?php
/**
 * View for the Global Customization Tab
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// As variáveis $customization e $google_fonts são passadas do escopo de admin_page().
?>
<form method="post" action="">
    <?php wp_nonce_field('timeline_customization_save', 'timeline_customization_nonce'); ?>
    
    <h3>Customização Global dos Itens</h3>
    <p class="description">Aplique estilos e classes a todos os itens da timeline.</p>
    
    <?php
    $fields = [
        'year'        => 'Ano (título do conteúdo)',
        'description' => 'Descrição',
        'data_text'   => 'Texto Lateral',
        'image'       => 'Imagem'
    ];

    foreach ($fields as $key => $label):
    ?>
        <div class="timeline-customization-section">
            <h4><?php echo esc_html($label); ?></h4>
            <table class="form-table">
                <?php if ($key === 'image'): ?>
                    <tr>
                        <th scope="row">Opções da Imagem</th>
                        <td>
                            <label>Raio da Borda (px) 
                                <input type="number" name="timeline_customization[image_border_radius]" value="<?php echo esc_attr($customization['image_border_radius'] ?? '0'); ?>" class="small-text" />
                            </label><br>
                            <label>
                                <input type="checkbox" name="timeline_customization[image_shadow]" value="1" <?php checked(isset($customization['image_shadow']), true); ?> /> Sombra (shadow)
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Classe CSS Customizada</th>
                        <td><input type="text" name="timeline_customization[image_class]" value="<?php echo esc_attr($customization['image_class'] ?? ''); ?>" class="regular-text" placeholder="ex: minha-classe" /></td>
                    </tr>
                <?php else: 
                    // Pega os valores salvos ou define padrões
                    $font_family      = $customization[$key . '_font_family'] ?? 'default';
                    $font_weight      = $customization[$key . '_font_weight'] ?? 'regular';
                    $font_size        = $customization[$key . '_font_size'] ?? '';
                    $font_size_unit   = $customization[$key . '_font_size_unit'] ?? 'px';
                    $line_height      = $customization[$key . '_line_height'] ?? '';
                    $line_height_unit = $customization[$key . '_line_height_unit'] ?? 'px';
                    $letter_spacing   = $customization[$key . '_letter_spacing'] ?? '';
                    $letter_spacing_unit = $customization[$key . '_letter_spacing_unit'] ?? 'px';
                    $text_transform   = $customization[$key . '_text_transform'] ?? 'none';
                ?>
                    <tr>
                        <th scope="row">Tipografia</th>
                        <td>
                            <div class="font-control-group">
                                <select name="timeline_customization[<?php echo $key; ?>_font_family]">
                                    <option value="default">Padrão do Tema</option>
                                    <?php if (!empty($google_fonts)): ?>
                                        <?php foreach ($google_fonts as $font => $details): ?>
                                            <option value="<?php echo esc_attr($font); ?>" <?php selected($font_family, $font); ?>><?php echo esc_html($font); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                                <select name="timeline_customization[<?php echo $key; ?>_font_weight]">
                                    <?php
                                    $weights = ['100','200','300','regular','400','500','600','700','800','900','100italic','200italic','300italic','italic','500italic','600italic','700italic','800italic','900italic'];
                                    foreach ($weights as $w) {
                                        echo "<option value='{$w}' " . selected($font_weight, $w, false) . ">{$w}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="font-control-group">
                                <input type="number" class="small-text" name="timeline_customization[<?php echo $key; ?>_font_size]" value="<?php echo esc_attr($font_size); ?>" placeholder="Tamanho">
                                <select name="timeline_customization[<?php echo $key; ?>_font_size_unit]">
                                    <option value="px" <?php selected($font_size_unit, 'px'); ?>>px</option>
                                    <option value="em" <?php selected($font_size_unit, 'em'); ?>>em</option>
                                    <option value="rem" <?php selected($font_size_unit, 'rem'); ?>>rem</option>
                                </select>

                                <input type="number" class="small-text" name="timeline_customization[<?php echo $key; ?>_line_height]" value="<?php echo esc_attr($line_height); ?>" placeholder="Altura Linha" step="0.1">
                                <select name="timeline_customization[<?php echo $key; ?>_line_height_unit]">
                                   <option value="px" <?php selected($line_height_unit, 'px'); ?>>px</option>
                                   <option value="em" <?php selected($line_height_unit, 'em'); ?>>em</option>
                                   <option value="rem" <?php selected($line_height_unit, 'rem'); ?>>rem</option>
                                </select>

                                <input type="number" class="small-text" name="timeline_customization[<?php echo $key; ?>_letter_spacing]" value="<?php echo esc_attr($letter_spacing); ?>" placeholder="Esp. Letras" step="0.1">
                                <select name="timeline_customization[<?php echo $key; ?>_letter_spacing_unit]">
                                    <option value="px" <?php selected($letter_spacing_unit, 'px'); ?>>px</option>
                                    <option value="em" <?php selected($letter_spacing_unit, 'em'); ?>>em</option>
                                    <option value="rem" <?php selected($letter_spacing_unit, 'rem'); ?>>rem</option>
                                </select>
                                
                                <select name="timeline_customization[<?php echo $key; ?>_text_transform]">
                                    <option value="none" <?php selected($text_transform, 'none'); ?>>Normal</option>
                                    <option value="uppercase" <?php selected($text_transform, 'uppercase'); ?>>MAIÚSCULAS</option>
                                    <option value="lowercase" <?php selected($text_transform, 'lowercase'); ?>>minúsculas</option>
                                    <option value="capitalize" <?php selected($text_transform, 'capitalize'); ?>>Capitalizado</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Cor & Estilo</th>
                        <td>
                            <input type="color" name="timeline_customization[<?php echo $key; ?>_color]" value="<?php echo esc_attr($customization[$key . '_color'] ?? '#ffffff'); ?>" />
                            <label><input type="checkbox" name="timeline_customization[<?php echo $key; ?>_shadow]" value="1" <?php checked(isset($customization[$key . '_shadow']), true); ?> /> Sombra de texto</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Classe CSS Customizada</th>
                        <td><input type="text" name="timeline_customization[<?php echo $key; ?>_class]" value="<?php echo esc_attr($customization[$key . '_class'] ?? ''); ?>" class="regular-text" placeholder="ex: minha-classe" /></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    <?php endforeach; ?>
    
    <?php submit_button('Salvar Customizações', 'primary', 'submit_customization'); ?>
</form> 