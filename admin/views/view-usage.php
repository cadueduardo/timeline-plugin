<?php
/**
 * View for the Usage Tab
 *
 * @package timeline-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<h2>Uso & Suporte</h2>

<div class="usage-section">
    <h3>Verificação de Atualização</h3>
    <p>O WordPress verifica por atualizações de plugins periodicamente. Se você sabe que uma nova versão do plugin foi lançada no GitHub mas a notificação de atualização ainda não apareceu no painel, você pode forçar uma nova verificação.</p>
    <?php
    $check_url = wp_nonce_url(admin_url('admin.php?page=timeline-plugin&force-check-update=true'), 'timeline_force_check');
    ?>
    <a href="<?php echo esc_url($check_url); ?>" class="button button-secondary">Forçar Verificação de Atualização</a>
    <p class="description">
        Isso limpa o cache de atualizações do WordPress e força uma nova busca por versões.
    </p>
</div>

<div class="usage-section">
    <h3>Shortcode</h3>
    <p>Para exibir a timeline em qualquer página ou post, utilize o shortcode abaixo:</p>
    <code>[timeline title="Nossa História" subtitle="Um legado de conquistas"]</code>
    <ul>
        <li><code>title</code>: (Opcional) O título principal exibido acima da timeline.</li>
        <li><code>subtitle</code>: (Opcional) O subtítulo exibido abaixo do título principal.</li>
    </ul>
</div>

<div class="usage-section">
    <h3>Suporte</h3>
    <p>Encontrou um bug ou tem uma sugestão? Abra uma "Issue" no nosso repositório do GitHub.</p>
    <a href="https://github.com/cadueduardo/timeline-plugin/issues" class="button" target="_blank">Abrir uma Issue no GitHub</a>
</div>

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
    <li><strong>Sem Background</strong>: Não há background, apenas o conteúdo da timeline</li>
</ul> 