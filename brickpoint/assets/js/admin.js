/* BrickPoint admin: media pickers for term/post meta. */
(function ($) {
  'use strict';
  $(document).on('click', '.bp-media-select', function (e) {
    e.preventDefault();
    var wrap = $(this).closest('.bp-media-field');
    var frame = wp.media({ title: 'Select image', multiple: false, library: { type: 'image' } });
    frame.on('select', function () {
      var att = frame.state().get('selection').first().toJSON();
      wrap.find('input').val(att.id);
      var src = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
      wrap.find('.bp-media-preview').attr('src', src).show();
      wrap.find('.bp-media-remove').show();
    });
    frame.open();
  });
  $(document).on('click', '.bp-media-remove', function (e) {
    e.preventDefault();
    var wrap = $(this).closest('.bp-media-field');
    wrap.find('input').val('');
    wrap.find('.bp-media-preview').hide();
    $(this).hide();
  });
  $(document).on('click', '.bp-gallery-select', function (e) {
    e.preventDefault();
    var wrap = $(this).closest('.bp-gallery-field');
    var input = wrap.find('input');
    var frame = wp.media({ title: 'Select images', multiple: 'add', library: { type: 'image' } });
    frame.on('select', function () {
      var ids = input.val() ? input.val().split(',') : [];
      var preview = wrap.find('.bp-gallery-preview');
      frame.state().get('selection').each(function (att) {
        att = att.toJSON();
        if (ids.indexOf(String(att.id)) === -1) {
          ids.push(String(att.id));
          var src = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
          preview.append('<img src="' + src + '" alt="" />');
        }
      });
      input.val(ids.join(','));
    });
    frame.open();
  });
  $(document).on('click', '.bp-gallery-clear', function (e) {
    e.preventDefault();
    var wrap = $(this).closest('.bp-gallery-field');
    wrap.find('input').val('');
    wrap.find('.bp-gallery-preview').empty();
  });
})(jQuery);
