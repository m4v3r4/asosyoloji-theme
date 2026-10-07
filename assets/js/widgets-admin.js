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

      $widget.on('click', '[data-magazine-select-pdf]', function () {
        const $row = $(this).closest('[data-magazine-row]');
        const frame = wp.media({
          title: 'PDF seç',
          button: { text: 'PDF kullan' },
          library: { type: 'application/pdf' },
          multiple: false
        });

        frame.on('select', function () {
          const attachment = frame.state().get('selection').first().toJSON();
          const $title = $row.find('[data-magazine-title]');
          const $cover = $row.find('[data-magazine-cover-url]');

          $row.find('[data-magazine-pdf-url]').val(attachment.url).trigger('change');
          $row.find('[data-magazine-attachment-id]').val(attachment.id || 0).trigger('change');

          if (!$title.val()) {
            $title.val(attachment.title || attachment.filename || '').trigger('change');
          }

          if (!$cover.val()) {
            let preview = '';
            if (attachment.sizes) {
              if (attachment.sizes.large && attachment.sizes.large.url) {
                preview = attachment.sizes.large.url;
              } else if (attachment.sizes.medium && attachment.sizes.medium.url) {
                preview = attachment.sizes.medium.url;
              } else if (attachment.sizes.full && attachment.sizes.full.url) {
                preview = attachment.sizes.full.url;
              }
            }

            if (preview) {
              $cover.val(preview).trigger('change');
            }
          }
        });

        frame.open();
      });

      $widget.on('click', '[data-magazine-select-cover]', function () {
        const $row = $(this).closest('[data-magazine-row]');
        const frame = wp.media({
          title: 'Kapak görseli seç',
          button: { text: 'Görseli kullan' },
          library: { type: 'image' },
          multiple: false
        });

        frame.on('select', function () {
          const attachment = frame.state().get('selection').first().toJSON();
          $row.find('[data-magazine-cover-url]').val(attachment.url).trigger('change');
        });

        frame.open();
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
