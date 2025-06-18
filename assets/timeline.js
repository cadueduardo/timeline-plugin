(function($) {
    // Timeline com background dinâmico (original)
    $.fn.timeline = function() {
      var selectors = {
        id: $(this),
        item: $(this).find(".timeline-item"),
        activeClass: "timeline-item--active",
        img: ".timeline__img"
      };
      selectors.item.eq(0).addClass(selectors.activeClass);
      selectors.id.css(
        "background-image",
        "url(" +
          selectors.item
            .first()
            .find(selectors.img)
            .attr("src") +
          ")"
      );
      var itemLength = selectors.item.length;
      // Detecta se está em modal
      var scrollContainer = selectors.id.closest('.modal-content');
      if (scrollContainer.length === 0) scrollContainer = $(window);
      scrollContainer.on('scroll.timeline', function() {
        console.log('Scroll detectado no container:', scrollContainer.get(0));
        var containerScrollTop = scrollContainer.scrollTop();
        var containerOffset = scrollContainer.offset() ? scrollContainer.offset().top : 0;
        var containerHeight = scrollContainer.height();
        var scrollMiddle = containerScrollTop + containerHeight / 2;
        selectors.item.each(function(i) {
          var itemOffset = $(this).offset().top - containerOffset + containerScrollTop;
          var itemHeight = $(this).outerHeight();
          var min = itemOffset;
          var max = min + itemHeight;
          if (i == itemLength - 2 && scrollMiddle > min + itemHeight / 2) {
            selectors.item.removeClass(selectors.activeClass);
            selectors.id.css(
              "background-image",
              "url(" +
                selectors.item
                  .last()
                  .find(selectors.img)
                  .attr("src") +
                ")"
            );
            selectors.item.last().addClass(selectors.activeClass);
          } else if (scrollMiddle <= max && scrollMiddle >= min) {
            selectors.id.css(
              "background-image",
              "url(" +
                $(this)
                  .find(selectors.img)
                  .attr("src") +
                ")"
            );
            selectors.item.removeClass(selectors.activeClass);
            $(this).addClass(selectors.activeClass);
          }
        });
      });
    };
    
    // Timeline com background estático (apenas animações)
    $.fn.timelineStatic = function() {
      var selectors = {
        id: $(this),
        item: $(this).find(".timeline-item"),
        activeClass: "timeline-item--active",
        img: ".timeline__img"
      };
      selectors.item.eq(0).addClass(selectors.activeClass);
      var itemLength = selectors.item.length;
      var scrollContainer = selectors.id.closest('.modal-content');
      if (scrollContainer.length === 0) scrollContainer = $(window);
      scrollContainer.on('scroll.timeline', function() {
        console.log('Scroll detectado no container:', scrollContainer.get(0));
        var containerScrollTop = scrollContainer.scrollTop();
        var containerOffset = scrollContainer.offset() ? scrollContainer.offset().top : 0;
        var containerHeight = scrollContainer.height();
        var scrollMiddle = containerScrollTop + containerHeight / 2;
        selectors.item.each(function(i) {
          var itemOffset = $(this).offset().top - containerOffset + containerScrollTop;
          var itemHeight = $(this).outerHeight();
          var min = itemOffset;
          var max = min + itemHeight;
          if (i == itemLength - 2 && scrollMiddle > min + itemHeight / 2) {
            selectors.item.removeClass(selectors.activeClass);
            selectors.item.last().addClass(selectors.activeClass);
          } else if (scrollMiddle <= max && scrollMiddle >= min) {
            selectors.item.removeClass(selectors.activeClass);
            $(this).addClass(selectors.activeClass);
          }
        });
      });
    };
    
    // Reinicializar timeline ao abrir modal
    $(document).on('click', '.open-modal', function() {
      setTimeout(function() {
        $('.timeline-container').each(function() {
          if (typeof $.fn.timeline === 'function') {
            $(this).removeClass('timeline-initialized');
            $(this).off('.timeline');
            $(this).timeline();
            $(this).addClass('timeline-initialized');
          }
        });
      }, 500);
    });
    // Também para modais que disparam evento customizado
    $(document).on('modal:opened', function() {
      setTimeout(function() {
        $('.timeline-container').each(function() {
          if (typeof $.fn.timeline === 'function') {
            $(this).removeClass('timeline-initialized');
            $(this).off('.timeline');
            $(this).timeline();
            $(this).addClass('timeline-initialized');
          }
        });
      }, 300);
    });
  })(jQuery); 