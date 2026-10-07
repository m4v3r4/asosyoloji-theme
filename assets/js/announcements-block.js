(function (blocks, element, components, blockEditor, i18n) {
  const el = element.createElement;
  const InspectorControls = blockEditor.InspectorControls;
  const PanelBody = components.PanelBody;
  const TextControl = components.TextControl;
  const SelectControl = components.SelectControl;
  const RangeControl = components.RangeControl;
  const ToggleControl = components.ToggleControl;
  const __ = i18n.__;

  const categories =
    window.asosyolojiAnnouncementsBlock && Array.isArray(window.asosyolojiAnnouncementsBlock.categories)
      ? window.asosyolojiAnnouncementsBlock.categories
      : [{ label: __('Varsayılan: duyurular', 'asosyoloji'), value: 0 }];

  blocks.registerBlockType('asosyoloji/announcements', {
    apiVersion: 3,
    title: __('Asosyoloji: Duyurular', 'asosyoloji'),
    icon: 'megaphone',
    category: 'asosyoloji',
    description: __('Duyuru kategorisindeki yazıları ilan/pano görünümünde gösterir.', 'asosyoloji'),
    attributes: {
      title: { type: 'string', default: 'Duyurular' },
      category: { type: 'number', default: 0 },
      count: { type: 'number', default: 5 },
      showExcerpt: { type: 'boolean', default: true },
      showDate: { type: 'boolean', default: true },
      showButton: { type: 'boolean', default: true },
      compact: { type: 'boolean', default: false }
    },

    edit: function (props) {
      const a = props.attributes;

      return el(
        element.Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __('Duyuru ayarları', 'asosyoloji'), initialOpen: true },
            el(TextControl, {
              label: __('Başlık', 'asosyoloji'),
              value: a.title || '',
              onChange: function (value) {
                props.setAttributes({ title: value });
              }
            }),
            el(SelectControl, {
              label: __('Duyuru kategorisi', 'asosyoloji'),
              value: a.category || 0,
              options: categories,
              onChange: function (value) {
                props.setAttributes({ category: parseInt(value, 10) || 0 });
              }
            }),
            el(RangeControl, {
              label: __('Duyuru sayısı', 'asosyoloji'),
              value: a.count || 5,
              min: 1,
              max: 12,
              onChange: function (value) {
                props.setAttributes({ count: value || 5 });
              }
            }),
            el(ToggleControl, {
              label: __('Tarih rozetini göster', 'asosyoloji'),
              checked: !!a.showDate,
              onChange: function (value) {
                props.setAttributes({ showDate: value });
              }
            }),
            el(ToggleControl, {
              label: __('Kısa açıklamayı göster', 'asosyoloji'),
              checked: !!a.showExcerpt,
              onChange: function (value) {
                props.setAttributes({ showExcerpt: value });
              }
            }),
            el(ToggleControl, {
              label: __('Detay bağlantısını göster', 'asosyoloji'),
              checked: !!a.showButton,
              onChange: function (value) {
                props.setAttributes({ showButton: value });
              }
            }),
            el(ToggleControl, {
              label: __('Kompakt görünüm', 'asosyoloji'),
              checked: !!a.compact,
              onChange: function (value) {
                props.setAttributes({ compact: value });
              }
            })
          )
        ),
        el(
          'div',
          { className: 'aso-block-preview' },
          el('div', { className: 'aso-block-preview__label' }, __('ASOSYOLOJİ DUYURULAR', 'asosyoloji')),
          el('strong', {}, a.title || __('Duyurular', 'asosyoloji')),
          el(
            'p',
            {},
            __('Ön yüzde tarih rozetli, duyuru etiketi ve ilan/pano görünümüyle dinamik olarak render edilir.', 'asosyoloji')
          )
        )
      );
    },

    save: function () {
      return null;
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.i18n);
