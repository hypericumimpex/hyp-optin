window.AdminMasterPopup = (function (window, document, $) {
  //Document Ready
  var xbox;
  var app = {
    debug: true,
    prefix: 'mpp_',
  };

  app.init = function () {
    app.$post_body_audience = $('body.post-type-mpp_audience #post-body');
    app.$post_body_popup_editor = $('body.post-type-master-popups #post-body');

    //Save post
    app.$post_body_audience.on('click', '#save-popup', app.submit_save_audience_list);

    app.$post_body_audience.on('click', '.ampp-get-lists', app.get_lists_service);
    app.$post_body_audience.on('click', '.ampp-delete-subscriber', app.delete_subscriber);

    $(document).on('click', '.ampp-duplicate-popup', app.duplicate_popup);

    $(document).on('click', '.ampp-row-list-id', function (event) {
      app.$post_body_audience.find('.xbox-row-id-mpp_list-id .xbox-element').val($(this).data('list-id'));
      $(this).closest('.xbox-confirm').find('.xbox-confirm-close-btn').trigger('click');
    });

    $('body.wp-admin').on('click', '.ampp-close-message', app.close_info_message);

    app.$post_body_audience.on('ifClicked', '.xbox-field-id-mpp_service .xbox-radiochecks input', function (event) {
      if( $(this).val() !== 'drip' ){
        return;
      }
      app.get_drip_accounts(event);
    });

    app.manage_popup_templates();

    $('.mpp-datatable').DataTable({
      "dom": "lfrtipB",
      "buttons": [{
        extend: 'csv',
        text: 'Export CSV',
        className: 'xbox-btn xbox-btn-teal'
      }],
      'lengthMenu': [[50, 100, 200, 500, -1], [50, 100, 200, 500, "All"]],
      'pageLength': 50,
      "oLanguage": {
        "sLengthMenu": "Display _MENU_ subscribers",
        "sZeroRecords": "No subscribers found",
        "sInfo": "Showing _START_ to _END_ of _TOTAL_ subscribers",
        "sInfoFiltered": " - filtering from _MAX_ subscribers",
        "sInfoEmpty": "No subscribers to show",
      }
    });
  };

  app.get_drip_accounts = function (event) {
    var $account_field = app.$post_body_audience.find('.xbox-field-id-mpp_account-id');
    var accounts = $account_field.data('accounts');
    var $dropdown = $account_field.find('.ui.selection.dropdown');
    if( accounts ){
      return;
    }
    app.ajax({
      data: {
        action: 'mpp_get_drip_accounts',
        service: 'drip',
      },
      beforeSend: function () {
        $dropdown.addClass('loading');
      },
      success: function(response){
        if( response.success && response.accounts ){
          var accounts = response.accounts;
          $account_field.data('accounts', accounts);
          var values = [];
          for (var key in accounts) {
            if (accounts.hasOwnProperty(key)) {
              values.push({
                value: key,
                name: key+' - '+accounts[key],
              });
            }
          }
          $dropdown.dropdownXbox( 'setup menu', {values: values});
        } else {
          alert(response.message);
        }
      },
      complete: function(){
        $dropdown.removeClass('loading');
      }
    });
  };

  app.close_info_message = function (event) {
    var selector = $(this).hasClass('ampp-close-row') ? '.xbox-row' : '.ampp-message';
    $(this).closest(selector).fadeOut(200);
  };

  app.message = function (type, icon, header, content) {
    var message_class = 'ampp-message ampp-message-' + type;
    if (icon === true) {
      message_class += ' ampp-icon-message';
    }
    var message = '<div class="' + message_class + '">';
    message += '<i class="xbox-icon xbox-icon-remove ampp-close-message"></i>';
    if (header) {
      message += '<header>' + header + '</header>';
    }
    message += '<p>' + content + '</p>';
    message += '</div>';
    return message;
  };

  app.duplicate_popup = function (event) {
    event.preventDefault();
    var $btn = $(this);
    $btn.removeClass('ampp-duplicate-popup');
    var data = {
      ajax_nonce: MPP_ADMIN_JS.ajax_nonce,
      action: 'mpp_duplicate_popup',
      popup_id: $btn.data('popup_id'),
    }
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: MPP_ADMIN_JS.ajax_url,
      data: data,
      beforeSend: function () {
      },
      success: function (response) {
        c(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
      },
      complete: function (jqXHR, textStatus) {
        location.reload();
      }
    });
  }

  app.submit_save_audience_list = function (event) {
    event.preventDefault();
    var $btn = $(this);
    $btn.find('i').remove();
    $btn.append("<i class='mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader'></i>");
    var fields = app.audience_fields();

    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.saving_changes,
      content: MPP_ADMIN_JS.text.please_wait,
      hide_confirm: true,
      hide_cancel: true,
      hide_close: true,
      wrap_class: 'ampp-transparent-confirm',
    });

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_check_list_id_service',
      service: fields.service.value,
      list_id: fields.list_id.value,
      account_id: fields.account_id.value,//for Drip integration
    };

    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: data,
      beforeSend: function () {
      },
      success: function (response) {
        if (response && response.connected) {//Sólo cambiar estado cuando se logra conectar con el servicio
          if (response.success) {
            xbox.set_field_value(fields.list_status.field, 'on');
          } else {
            xbox.set_field_value(fields.list_status.field, 'off');
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
        if (fields.service.value == 'master_popups') {
          xbox.set_field_value(fields.list_status.field, 'on');
        }
        //Save post
        $('#publish').click();
      }
    });
    //Callback, por demora en la conexion a cualquier servicio
    setTimeout(function () {
      $('#publish').click();
    }, 13000);
  };

  app.manage_popup_templates = function () {
    var $control = app.$post_body_popup_editor.find('.ampp-control-popup-templates');
    var $wrap = app.$post_body_popup_editor.find('.ampp-wrap-popup-templates');
    var $categories = app.$post_body_popup_editor.find('.ampp-categories-popup-templates');
    var $tags = app.$post_body_popup_editor.find('.ampp-tags-popup-templates');

    $control.on('click', 'ul li', function (event) {
      var $btn = $(this);
      $btn.addClass('ampp-active').siblings().removeClass('ampp-active');
      var filter_category = $categories.find('.ampp-active').data('filter');
      var filter_tag = $tags.find('.ampp-active').data('filter');

      var $items = $wrap.find('.ampp-item-popup-template').filter(function (index) {
        var data_category = $(this).data('category');
        var data_tags = $(this).data('tags');
        return data_category.indexOf(filter_category) > -1 && data_tags.indexOf(filter_tag) > -1;
      });

      $wrap.fadeTo(150, 0.15);
      $wrap.find('.ampp-item-popup-template').fadeOut(400).removeClass('ampp-scale-1');
      setTimeout(function () {
        $items.fadeIn(350).addClass('ampp-scale-1');
        $wrap.fadeTo(300, 1);
      }, 300);
    });

    $wrap.on('click', '.ampp-item-popup-template', function (event) {
      $(this).addClass('ampp-active').siblings().removeClass('ampp-active');
      var json_url = $(this).data('url');
      $('input[name="mpp_xbox-import-field"]').eq(0).val(json_url);
      if ($('input[name="xbox-import-url"]').length) {
        $('input[name="xbox-import-url"]').eq(0).val(json_url);
      }
    });
  };

  app.get_lists_service = function (event) {
    var $btn = $(this);
    var fields = app.audience_fields();

    var data = {
      ajax_nonce: XBOX_JS.ajax_nonce,
      action: 'mpp_get_lists_service',
      service: fields.service.value,
      account_id: fields.account_id.value,//for Drip integration
    };

    $.xboxConfirm({
      title: MPP_ADMIN_JS.text.service.title_popup_get_lists,
      content: {
        data: data,
        dataType: 'json',
        url: XBOX_JS.ajax_url,
        onSuccess: function (response) {
          c(response);
          var $wrap = $('.ampp-wrap-service-lists .xbox-confirm-content');
          if (response && response.success && !$.isEmptyObject(response.lists)) {
            var html = '<table class="ampp-table ampp-center">';
            html += '<tr><th>List ID</th><th>List Name</th></tr>';
            $.each(response.lists, function (list_id, list_name) {
              html += '<tr class="ampp-row-list-id" data-list-id="' + list_id + '"><td>' + list_id + '</td><td>' + list_name + '</td></tr>';
            });
            html += '</table>';
            $wrap.html('<p>' + response.message + '</p>' + html);
          } else {
            $wrap.html('<p>' + response.message + '</p>');
          }
        }
      },
      hide_confirm: true,
      hide_cancel: true,
      wrap_class: 'ampp ampp-wrap-service-lists',
    });
  };

  app.delete_subscriber = function (event) {
    event.preventDefault();
    $.xboxConfirm({
      title: XBOX_JS.text.remove_item_popup.title,
      content: XBOX_JS.text.remove_item_popup.content,
      confirm_class: 'xbox-btn-blue',
      confirm_text: XBOX_JS.text.popup.accept_button,
      cancel_text: XBOX_JS.text.popup.cancel_button,
      onConfirm: function () {
        app._delete_subscriber(event);
      }
    });
    return false;
  };

  app._delete_subscriber = function (event) {
    var $btn = $(event.currentTarget);
    var $tr = $btn.closest('tr');
    var email = $tr.find('td[data-email]').data('email');
    var audience_id = $tr.closest('table').data('audience-id');
    var data = {
      action: 'mpp_delete_subscriber',
      audience_id: $tr.closest('table').data('audience-id'),
      email: $tr.find('td[data-email]').data('email'),
    };
    app.ajax({
      data: data,
      beforeSend: function () {
        $btn.find('i').attr('class', '').addClass('mpp-icon mpp-icon-spinner mpp-icon-spin ampp-loader xbox-color-dark');
      },
      success: function (response) {
        if (response && response.success) {
          $tr.fadeOut(600, function () {
            $tr.remove();
          });
          app.$post_body_audience.find('.ampp-total-subscribers span').text(response.total);
        }
      },
      complete: function (jqXHR, textStatus) {
        $btn.find('i').attr('class', '').addClass('xbox-icon xbox-icon-trash xbox-color-red');
      }
    });
  };

  app.audience_fields = function () {
    var $service = app.$post_body_audience.find('.xbox-field-id-mpp_service');
    var $account_id = app.$post_body_audience.find('.xbox-field-id-mpp_account-id');
    var $list_id = app.$post_body_audience.find('.xbox-field-id-mpp_list-id');
    var $list_status = app.$post_body_audience.find('.xbox-field-id-mpp_list-status')
    return {
      service: {
        field: $service,
        value: $service.find('.xbox-element:checked').val(),
      },
      account_id: {
        field: $account_id,
        value: $account_id.find('.xbox-element input[type="hidden"]').val()
      },
      list_id: {
        field: $list_id,
        value: $list_id.find('.xbox-element').val(),
      },
      list_status: {
        field: $list_status,
        value: $list_status.find('.xbox-element').val(),
      }
    }
  };

  app.set_focus_end = function ($el) {
    var value = $el.val();
    $el.focus();
    $el.val('');
    $el.val(value);
  };

  app.scroll_to = function ($this, delay, offset, callback) {
    offset = offset || 300;
    delay = delay || 650;
    $('html,body').animate({ scrollTop: Math.abs($this.offset().top - offset) }, delay, callback);
    return false;
  };

  app.focus_without_scrolling = function (elem) {
    var x = window.scrollX, y = window.scrollY;
    elem.focus();
    window.scrollTo(x, y);
  };

  app.get_unit = function ($target) {
    return $target.closest('.xbox-field').find('input.xbox-unit-number').val();
  };

  app.number_object = function (value) {
    var number = {
      value: value,
      unit: undefined,
    };
    value = value.toString();
    if ($.inArray(value, ['auto', 'initial', 'inherit', 'normal']) > -1) {
      number.value = value;
      number.unit = undefined;
    } else if (value.indexOf('px') > -1) {
      number.value = value.replace('px', '');
      number.unit = 'px';
    } else if (value.indexOf('%') > -1) {
      number.value = value.replace('%', '');
      number.unit = '%';
    } else if (value.indexOf('em') > -1) {
      number.value = value.replace('em', '');
      number.unit = 'em';
    }
    return number;
  };

  app.is_number = function (n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  };

  app.css = {
    number: function (value, unit) {
      unit = unit || '';
      var arr = ['auto', 'initial', 'inherit', 'normal'];
      if ($.inArray(value, arr) > -1) {
        return value;
      }
      value = value.toString().replace(/[^0-9.\-]/g, '');
      if (this.is_number(value)) {
        return value + unit;
      }
      return 1;
    },
    is_number: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },
  };

  app.ajax = function (options) {
    var defaults = {
      type: 'post',
      data: {
        ajax_nonce: XBOX_JS.ajax_nonce,
      },
      dataType: 'json',
      beforeSend: function () {
      },
      success: function (response) {
      },
      complete: function (jqXHR, textStatus) {
      },
    };
    options = $.extend(true, {}, defaults, options);
    $.ajax({
      url: XBOX_JS.ajax_url,
      type: options.type,
      dataType: options.dataType,
      data: options.data,
      beforeSend: options.beforeSend,
      success: function (response) {
        cc('Ajax Success', response);
        if ($.isFunction(options.success)) {
          options.success.call(this, response);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cc('Ajax Error, textStatus=', textStatus);
        cc('jqXHR', jqXHR);
        cc('jqXHR.responseText', jqXHR.responseText);
        cc('errorThrown', errorThrown);
      },
      complete: function (jqXHR, textStatus) {
        if ($.isFunction(options.complete)) {
          options.complete.call(this, jqXHR, textStatus);
        }
      }
    });
  };

  app.ajax_example = function () {
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: XBOX_JS.ajax_url,
      data: {
        action: 'mpp_action',
        data: data,
        ajax_nonce: XBOX_JS.ajax_nonce
      },
      beforeSend: function () {
      },
      success: function (response) {
        if (response) {
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function (jqXHR, textStatus) {
      }
    });
  };

  app.is_empty = function (value) {
    if (value === undefined || value === null) {
      return true;
    } else if (typeof value == 'object' && value instanceof $) {
      return value.length === 0;
    } else {
      return (value === false || $.trim(value).length === 0);
    }
  };

  //Funciones privadas
  function get_class_starts_with($elment, starts_with) {
    return $.grep($elment.attr('class').split(" "), function (v, i) {
      return v.indexOf(starts_with) === 0;
    }).join();
  }

  //Debug
  function c(msg) {
    console.log(msg);
  }

  function cc(msg, msg2) {
    console.log(msg, msg2);
  }

  function clog(msg) {
    if (app.debug) {
      console.log(msg);
    }
  }

  $(function () {
    xbox = window.XBOX;
    app.init();
  });

  return app;

})(window, document, jQuery);

//Insert text into textarea with jQuery
jQuery.fn.extend({
  insertTextInCursor: function (myValue) {
    return this.each(function (i) {
      if (document.selection) {
        //For browsers like Internet Explorer
        this.focus();
        var sel = document.selection.createRange();
        sel.text = myValue;
        this.focus();
      }
      else if (this.selectionStart || this.selectionStart == '0') {
        //For browsers like Firefox and Webkit based
        var startPos = this.selectionStart;
        var endPos = this.selectionEnd;
        var scrollTop = this.scrollTop;
        this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
        this.focus();
        this.selectionStart = startPos + myValue.length;
        this.selectionEnd = startPos + myValue.length;
        this.scrollTop = scrollTop;
      } else {
        this.value += myValue;
        this.focus();
      }
    });
  }
});

jQuery.fn.getValidationMessages = function (fields) {
  var message = "";
  var name = "";
  fields = fields || 'input, textarea';
  this.each(function () {
    $(this).find(fields).each(function (index, el) {
      if (el.checkValidity() === false) {
        name = $("label[for=" + el.id + "]").html() || el.placeholder || el.name || el.id;
        message = message + name + ": " + (this.validationMessage || 'Invalid value.') + "\n";
      }
    });

  });
  return message;
};
