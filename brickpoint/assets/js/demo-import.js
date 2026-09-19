/* BrickPoint Demo Importer UI — runs the import step by step over AJAX. */
(function ($) {
  'use strict';
  var cfg = window.brickpointDemo || {};
  var $btn, $steps, $bar, $log, $success, $error, running = false;

  function log(msg) {
    if (!$log.length) { return; }
    $log.append(msg + '\n');
    $log.scrollTop($log[0].scrollHeight);
  }

  function setStep(id, state, detail) {
    var $li = $steps.find('[data-step="' + id + '"]');
    $li.removeClass('running done error').addClass(state);
    if (detail !== undefined) { $li.find('.bp-step-detail').text(detail); }
  }

  function runStep(index, steps, ctx) {
    if (index >= steps.length) {
      finish(ctx);
      return;
    }
    var step = steps[index];
    setStep(step.id, 'running', '');
    $bar.css('width', Math.round((index / steps.length) * 100) + '%');
    $.ajax({
      url: cfg.ajaxUrl,
      method: 'POST',
      dataType: 'json',
      data: { action: 'brickpoint_demo_step', step: step.id, nonce: cfg.nonce, offset: ctx.offset || 0 },
      timeout: 0
    }).done(function (res) {
      if (!res || !res.success) {
        var msg = (res && res.data && res.data.message) ? res.data.message : cfg.i18n.error;
        setStep(step.id, 'error', '');
        log('✖ ' + step.label + ': ' + msg);
        fail(msg);
        return;
      }
      var d = res.data || {};
      log('✔ ' + step.label + (d.message ? ' — ' + d.message : ''));
      if (d.more) {
        /* step wants to continue (batching) */
        ctx.offset = d.offset || 0;
        setStep(step.id, 'running', d.message || '');
        runStep(index, steps, ctx);
        return;
      }
      ctx.offset = 0;
      setStep(step.id, 'done', d.message || '');
      if (d.links) { ctx.links = d.links; }
      runStep(index + 1, steps, ctx);
    }).fail(function (xhr) {
      setStep(step.id, 'error', '');
      var msg = cfg.i18n.error;
      if (xhr && xhr.responseText && xhr.responseText.length < 600) { msg += ' (' + xhr.status + ')'; }
      log('✖ ' + step.label + ' — HTTP ' + (xhr ? xhr.status : '?'));
      fail(msg);
    });
  }

  function fail(msg) {
    running = false;
    $btn.prop('disabled', false).text($btn.data('retry'));
    $error.text(msg).addClass('show');
  }

  function finish(ctx) {
    running = false;
    $bar.css('width', '100%');
    $btn.prop('disabled', false).text($btn.data('again'));
    if (ctx.links) {
      Object.keys(ctx.links).forEach(function (k) {
        $success.find('[data-link="' + k + '"]').attr('href', ctx.links[k]);
      });
    }
    $success.addClass('show');
    $('html, body').animate({ scrollTop: $success.offset().top - 60 }, 400);
  }

  $(function () {
    $btn = $('#bp-demo-import');
    $steps = $('#bp-demo-steps');
    $bar = $('#bp-demo-progress span');
    $log = $('#bp-demo-log');
    $success = $('#bp-demo-success');
    $error = $('#bp-demo-error');
    if (!$btn.length) { return; }
    var steps = $steps.find('li').map(function () { return { id: $(this).data('step'), label: $(this).find('.bp-step-label').text() }; }).get();
    $btn.on('click', function (e) {
      e.preventDefault();
      if (running) { return; }
      running = true;
      $error.removeClass('show');
      $success.removeClass('show');
      $steps.find('li').removeClass('running done error').find('.bp-step-detail').text('');
      $log.text('');
      $btn.prop('disabled', true).text(cfg.i18n.running);
      log('Starting BrickPoint demo import…');
      runStep(0, steps, { offset: 0 });
    });
  });
})(jQuery);
