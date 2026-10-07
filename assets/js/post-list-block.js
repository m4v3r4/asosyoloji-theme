(function (blocks, element, components, blockEditor, i18n) {
  const el = element.createElement;
  const InspectorControls = blockEditor.InspectorControls;
  const PanelBody = components.PanelBody;
  const TextControl = components.TextControl;
  const SelectControl = components.SelectControl;
  const RangeControl = components.RangeControl;
  const ToggleControl = components.ToggleControl;
  const CheckboxControl = components.CheckboxControl;
  const __ = i18n.__;

  const categories =
    window.asosyolojiPostListBlock && Array.isArray(window.asosyolojiPostListBlock.categories)
      ? window.asosyolojiPostListBlock.categories
      : [{ label: __('Tüm kategoriler', 'asosyoloji'), value: 0 }];

  blocks.registerBlockType('asosyoloji/post-list', {
    apiVersion: 3,
    title: __('Asosyoloji: Yazı Listesi', 'asosyoloji'),
    icon: 'excerpt-view',
    category: 'widgets',
    description: __('Kategori bazlı yazıları temayla uyumlu liste, grid veya manşet görünümünde gösterir.', 'asosyoloji'),
    attributes: {
      title: { type: 'string', default: '' },
      category: { type: 'number', default: 0 },
      excludeCategories: { type: 'array', default: [] },
      count: { type: 'number', default: 6 },
      layout: { type: 'string', default: 'grid' },
      showImage: { type: 'boolean', default: true },
      showExcerpt: { type: 'boolean', default: false },
      showMeta: { type: 'boolean', default: true }
    },

    edit: function (props) {
      const a = props.attributes;
      const excluded = Array.isArray(a.excludeCategories) ? a.excludeCategories : [];

      const controls = el(
        InspectorControls,
        {},
        el(
          PanelBody,
          { title: __('Yazı listesi ayarları', 'asosyoloji'), initialOpen: true },
          el(TextControl, {
            label: __('Başlık', 'asosyoloji'),
            value: a.title || '',
            onChange: function (value) {
              props.setAttributes({ title: value });
            }
          }),
          el(SelectControl, {
            label: __('Kategori', 'asosyoloji'),
            value: a.category || 0,
            options: categories,
            onChange: function (value) {
              props.setAttributes({ category: parseInt(value, 10) || 0 });
            }
          }),
          el(RangeControl, {
            label: __('Yazı sayısı', 'asosyoloji'),
            value: a.count || 6,
            min: 1,
            max: 12,
            onChange: function (value) {
              props.setAttributes({ count: value || 6 });
            }
          }),
          el(SelectControl, {
            label: __('Görünüm', 'asosyoloji'),
            value: a.layout || 'grid',
            options: [
              { label: __('Görselli liste', 'asosyoloji'), value: 'list' },
              { label: __('Kompakt liste', 'asosyoloji'), value: 'compact' },
              { label: __('Kart grid', 'asosyoloji'), value: 'grid' },
              { label: __('Bir büyük + liste', 'asosyoloji'), value: 'feature' }
            ],
            onChange: function (value) {
              props.setAttributes({ layout: value });
            }
          }),
          el(ToggleControl, {
            label: __('Görselleri göster', 'asosyoloji'),
            checked: !!a.showImage,
            onChange: function (value) {
              props.setAttributes({ showImage: value });
            }
          }),
          el(ToggleControl, {
            label: __('Özeti göster', 'asosyoloji'),
            checked: !!a.showExcerpt,
            onChange: function (value) {
              props.setAttributes({ showExcerpt: value });
            }
          }),
          el(ToggleControl, {
            label: __('Yazar ve tarihi göster', 'asosyoloji'),
            checked: !!a.showMeta,
            onChange: function (value) {
              props.setAttributes({ showMeta: value });
            }
          })
        ),
        el(
          PanelBody,
          { title: __('Hariç tutulacak kategoriler', 'asosyoloji'), initialOpen: false },
          categories
            .filter(function (item) {
              return Number(item.value) !== 0;
            })
            .map(function (item) {
              const id = Number(item.value);
              return el(CheckboxControl, {
                key: id,
                label: item.label,
                checked: excluded.indexOf(id) !== -1,
                onChange: function (checked) {
                  const next = checked
                    ? excluded.concat([id]).filter(function (value, index, arr) {
                        return arr.indexOf(value) === index;
                      })
                    : excluded.filter(function (value) {
                        return value !== id;
                      });
                  props.setAttributes({ excludeCategories: next });
                }
              });
            })
        )
      );

      const preview = el(
        'div',
        { className: 'aso-block-preview' },
        el('div', { className: 'aso-block-preview__label' }, __('ASOSYOLOJİ YAZI LİSTESİ', 'asosyoloji')),
        el('strong', {}, a.title || __('Başlıksız yazı listesi', 'asosyoloji')),
        el(
          'p',
          {},
          __('Ön yüzde tema kartlarıyla dinamik olarak render edilir. Kategori, görünüm ve hariç kategorileri sağ panelden ayarlayabilirsiniz.', 'asosyoloji')
        )
      );

      return el(element.Fragment, {}, controls, preview);
    },

    save: function () {
      return null;
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.i18n);
