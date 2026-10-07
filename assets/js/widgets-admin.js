(function ($) {
  function bindMagazineWidget(context) {
    $(context).find('[data-magazine-widget]').each(function () {
      const $widget = $(this);
      if ($widget.data('asoMagazineBound')) return;
      $widget.data('asoMagazineBound', true);

      $widget.on('click', '[data-magazine-add]', function () {
        const template = $widget.find('template[data-magazine-template]').html();
        const baseName = $widget.find('[data-magazine-name]').val();
        const index = Date.now();
        const html = template
          .replaceAll('__NAME__', baseName)
          .replaceAll('__INDEX__', String(index));
        $(html).insertBefore($widget.find('p').has('[data-magazine-add]'));
      });

      $widget.on('click', '[data-magazine-remove]', function () {
        $(this).closest('[data-magazine-row]').remove();
      });
    });
  }

  $(function () {
    bindMagazineWidget(document);
  });

  $(document).on('widget-added widget-updated', function (event, widget) {
    bindMagazineWidget(widget || document);
  });
})(jQuery);
