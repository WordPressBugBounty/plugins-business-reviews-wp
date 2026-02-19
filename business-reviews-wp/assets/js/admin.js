/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************!*\
  !*** ./src/js/admin.js ***!
  \*************************/
(function ($) {
  'use strict';
  /* rt tab active navigation */

  $(".rt-tab-nav li").on('click', 'a', function (e) {
    e.preventDefault();
    var $this = $(this),
        container = $this.parents('.rt-tab-container'),
        nav = container.children('.rt-tab-nav'),
        content = container.children(".rt-tab-content"),
        $id = $this.attr('href'); // console.log($id);

    content.hide();
    nav.find('li').removeClass('active');
    $this.parent().addClass('active');
    container.find($id).show();
  });

  if ($(".rt-select2").length && $.fn.select2) {
    $(".rt-select2").select2({
      dropdownAutoWidth: true
    });
  }

  thpShowHideScMeta();
  renderTpgPreview();
  $("#rtbr_meta").on('change', 'select, input', function () {
    renderTpgPreview();
  }).on("input propertychange", function () {
    renderTpgPreview();
  });
  var colorSlt = $("#rtbr_meta .rt-color");

  if (colorSlt.length && $.fn.wpColorPicker) {
    var cOptions = {
      defaultColor: false,
      change: function change(event, ui) {
        renderTpgPreview();
      },
      clear: function clear() {
        renderTpgPreview();
      },
      hide: true,
      palettes: true
    };
    colorSlt.wpColorPicker(cOptions);
  }

  $(document).on('change', 'input[type=checkbox]', function () {
    thpShowHideScMeta();

    if ($("#rt-tpg-pagination").checked) {
      $(".rt-field-wrapper.posts-per-page").show();
    } else {
      $(".rt-field-wrapper.posts-per-page").hide();
    }
  });

  function renderTpgPreview() {
    if ($("#rtbr_meta").length) {
      var data = $("#rtbr_meta").find('input[name],select[name],textarea[name]').serialize(),
          container = $("#rtbr-preview-container").find('.rt-tpg-container'),
          loader = container.find(".rt-content-loader"); // Add Shortcode ID

      data = data + '&' + $.param({
        'sc_id': $('#post_ID').val() || 0
      });
      $(".rt-response").addClass('loading');
      rtbrAjaxCall(null, 'rtbr_shortcode_layout_preview', data, function (data) {
        if (!data.error) {
          $("#rtbr-preview-container").html(data);
          loader.find('.rt-loading-overlay, .rt-loading').remove();
          loader.removeClass('tpg-pre-loader');
        }

        $(".rt-response").removeClass('loading');
      });
    }
  }

  function rtbrAjaxCall(element, action, arg, handle) {
    var data;
    if (action) data = "action=" + action;
    if (arg) data = arg + "&action=" + action;
    if (arg && !action) data = arg;
    var n = data.search(rtbr.nonceID);

    if (n < 0) {
      data = data + "&rtbr_nonce=" + rtbr.nonce;
    }

    $.ajax({
      type: "post",
      url: rtbr.ajaxurl,
      data: data,
      beforeSend: function beforeSend() {
        if (element) {
          $("<span class='rt-loading'> </span>").insertAfter(element);
        }
      },
      success: function success(data) {
        if (element) {
          element.next(".rt-loading").remove();
        }

        handle(data);
      }
    });
  }

  $("#rtbr-business-type, #rtbr-sc-layout").on("change", function (e) {
    thpShowHideScMeta();
  });

  function thpShowHideScMeta() {
    // multiple business
    var multiple_business = $("#rtbr-business-type").val();

    if (multiple_business === 'multiple') {
      $('#multi_business_holder').show();
    } else {
      $('#multi_business_holder').hide();
    } // layout


    var layout = $("#rtbr-sc-layout").val(); // rt-badge-floating 

    if (layout === 'badge-floating') {
      $('#floating_badge_pos_holder').show();
    } else {
      $('#floating_badge_pos_holder').hide();
    } // grid column


    switch (layout) {
      case "grid-one":
      case "grid-two":
      case "grid-three":
      case "grid-four":
      case "grid-five":
      case "grid-six":
      case "grid-seven":
      case "grid-eight":
      case "slider-one":
      case "slider-two":
      case "isotope-one":
        $('#grid_column_holder').show();
        break;

      default:
        $('#grid_column_holder').hide();
    } // badge bg color


    switch (layout) {
      case "badge-one":
      case "badge-two":
      case "badge-sidebar":
      case "rt-badge-floating":
        $('#badge_bg_holder').show();
        break;

      default:
        $('#badge_bg_holder').hide();
    } //pagination


    var pagination = $("#rt-rtbr-pagination").is(':checked');

    if (pagination) {
      $(".rt-field-wrapper.reviews-per-page").show();
      $(".rt-field-wrapper.review-display-limit").hide();
      console.log('True');
    } else {
      $(".rt-field-wrapper.reviews-per-page").hide();
      $(".rt-field-wrapper.review-display-limit").show();
    } //business_info


    var business_info = $("#rtbr-business-info").is(':checked');

    if (business_info) {
      $("#business_info_fields_holder").hide();
    } else {
      $("#business_info_fields_holder").show();
    }
  } // admin settings


  $(function () {
    if ($.fn.select2) {
      $('.rtbr-select2').select2();
    }

    if ($.fn.wpColorPicker) {
      $('.rtbr-color').wpColorPicker();
    }

    if ($.fn.rtFieldDependency) {
      $('[data-rt-depends]').rtFieldDependency();
    }

    $('.rtbr-setting-image-wrap').on('click', '.rtbr-add-image', function (e) {
      e.preventDefault();
      var self = $(this),
          target = self.parents('.rtbr-setting-image-wrap'),
          file_frame,
          image_data,
          json; // If an instance of file_frame already exists, then we can open it rather than creating a new instance

      if (undefined !== file_frame) {
        file_frame.open();
        return;
      } // Here, use the wp.media library to define the settings of the media uploader


      file_frame = wp.media.frames.file_frame = wp.media({
        frame: 'post',
        state: 'insert',
        multiple: false
      }); // Setup an event handler for what to do when an image has been selected

      file_frame.on('insert', function () {
        // Read the JSON data returned from the media uploader
        json = file_frame.state().get('selection').first().toJSON(); // First, make sure that we have the URL of an image to display

        if (0 > $.trim(json.url.length)) {
          return;
        }

        var imgUrl = typeof json.sizes.medium === "undefined" ? json.url : json.sizes.medium.url;
        target.find('.rtbr-setting-image-id').val(json.id);
        target.find('.image-preview-wrapper').html('<img src="' + imgUrl + '" alt="' + json.title + '" />');
      }); // Now display the actual file_frame

      file_frame.open();
    }); // Delete the image when "Remove Image" button clicked

    $('.rtbr-setting-image-wrap').on('click', '.rtbr-remove-image', function (e) {
      e.preventDefault();
      var self = $(this),
          target = self.parents('.rtbr-setting-image-wrap');

      if (confirm('Are you sure to delete?')) {
        target.find('.rtbr-setting-image-id').val('');
        target.find('.image-preview-wrapper img').attr('src', target.find('.image-preview-wrapper').data('placeholder'));
      }
    });
  }); //licensing_settings

  $(".rtbr-settings .rtbr-license-wrapper").on('click', '.rtbr-licensing-btn', function (e) {
    e.preventDefault();
    var self = $(this),
        type = self.hasClass('license_activate') ? 'license_activate' : 'license_deactivate';
    $.ajax({
      type: "POST",
      url: rtbr.ajaxurl,
      data: {
        action: 'rtbr_manage_licensing',
        type: type
      },
      beforeSend: function beforeSend() {
        self.addClass('loading');
        self.parents('.description').find(".rt-licence-msg").remove();
        $('<span class="rt-icon-spinner animate-spin"></span>').insertAfter(self);
      },
      success: function success(response) {
        self.next('.rt-icon-spinner').remove();
        self.removeClass('loading');

        if (!response.error) {
          self.text(response.value);
          self.removeClass(type);
          self.addClass(response.type);

          if (response.type == 'license_deactivate') {
            self.removeClass('button-primary');
            self.addClass('danger');
          } else if (response.type == 'license_activate') {
            self.removeClass('danger');
            self.addClass('button-primary');
          }
        }

        if (response.msg) {
          $("<span class='rt-licence-msg'>" + response.msg + "</span>").insertAfter(self);
        }

        self.blur();
      },
      error: function error(jqXHR, exception) {
        self.removeClass('loading');
        self.next('.rt-icon-spinner').remove();
      }
    });
  });
  $('#rtbr_tools_settings-license_key').on('keydown', function (e) {
    $(this).next().remove();
  });
})(jQuery);
/******/ })()
;