jQuery(function ($) {
  $('[data-aso-sortable]').each(function () {
    const $list = $(this);
    const $input = $list.next('input[type="hidden"]');

    $list.sortable({
      axis: 'y',
      handle: '.dashicons-menu',
      update: function () {
        const values = $list.children('[data-value]').map(function () {
          return $(this).data('value');
        }).get();
        $input.val(values.join(',')).trigger('change');
      }
    });
  });
});
