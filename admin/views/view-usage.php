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