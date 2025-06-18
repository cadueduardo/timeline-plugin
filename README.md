# Timeline Interativa - Plugin WordPress

Plugin WordPress para criar timelines interativas e responsivas baseado no design do CodePen.

## 📁 Estrutura do Plugin

```
timeline-plugin/
├── timeline-plugin.php      # Arquivo principal do plugin
├── assets/
│   ├── timeline.css        # Estilos da timeline
│   └── timeline.js         # JavaScript da timeline
└── README.md              # Este arquivo
```

## 🚀 Instalação

### Método 1: Upload via Admin WordPress
1. Comprima todos os arquivos em um arquivo `timeline-plugin.zip`
2. Vá para **Plugins > Adicionar Novo > Enviar Plugin**
3. Faça upload do arquivo ZIP
4. Ative o plugin

### Método 2: FTP/Upload Manual
1. Faça upload da pasta `timeline-plugin` para `/wp-content/plugins/`
2. Vá para **Plugins** no admin do WordPress
3. Ative o plugin "Timeline Interativa"

## 📖 Como Usar

### Shortcode Básico
```
[timeline]
```

### Shortcode com Parâmetros
```
[timeline title="Minha História" subtitle="Uma jornada incrível"]
```

### Parâmetros Disponíveis
- **title**: Título principal da timeline
- **subtitle**: Subtítulo (opcional)
- **id**: ID único para múltiplas timelines na mesma página

### Exemplo Completo
```
[timeline title="História da Empresa" subtitle="Desde 1990" id="timeline-empresa"]
```

## ⚙️ Configurações

Vá para **Configurações > Timeline** no admin do WordPress para ver todas as opções disponíveis.

## 🎨 Personalização

### CSS Customizado
Você pode personalizar a aparência adicionando CSS ao seu tema:

```css
/* Exemplo: Mudar cor do título */
.timeline-header__title {
    color: #your-color !important;
}

/* Exemplo: Mudar fonte dos itens */
.timeline__content-desc {
    font-family: 'Sua-Fonte', sans-serif !important;
}
```

### Modificar Itens da Timeline
Para personalizar os itens da timeline, edite a função `render_timeline_items()` no arquivo principal do plugin.

## 📱 Responsividade

A timeline é totalmente responsiva e se adapta automaticamente a dispositivos móveis.

## 🔧 Desenvolvimento

### Baseado em:
- **Pug**: Template engine convertido para HTML
- **SCSS**: Convertido para CSS puro
- **jQuery**: Para funcionalidades interativas

### Recursos:
- ✅ Totalmente responsivo
- ✅ Efeitos de scroll suaves
- ✅ Troca dinâmica de imagens de fundo
- ✅ Animações CSS
- ✅ Integração nativa com WordPress

## 🐛 Resolução de Problemas

### Timeline não aparece:
1. Verifique se o jQuery está carregado
2. Confirme se os arquivos CSS e JS estão sendo carregados
3. Verifique erros no console do navegador

### Imagens não carregam:
1. Verifique se as URLs das imagens estão corretas
2. Confirme se as imagens têm permissões adequadas

## 📄 Licença

GPL v2 ou posterior

## 👨‍💻 Suporte

Para suporte e customizações, entre em contato através do seu site. 