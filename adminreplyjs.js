jQuery("#response_form_submit_gl").submit(ajaxSubmit_response);
function ajaxSubmit_response(event) {
  event.preventDefault();
  var newsubmitform_response = jQuery(this).serialize();

  jQuery.ajax({
    type: "POST",
    url: my_ajax_object.ajax_url,
    data: newsubmitform_response,
    success: function (data) {
      jQuery("#response_form_submit_gl")[0].reset();
      jQuery("#wdm_chat_box_gl").append(data);
      jQuery("#wdm_chat_box_gl").scrollTop(jQuery("#wdm_chat_box_gl")[0].scrollHeight);
    },
  });

  jQuery("#response_form_submit_gl").on("keypress", function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
      e.preventDefault();
      document.getElementById("gl_response_button").click();
      return false;
    }
  });

  jQuery("#wdm_chat_box_gl").scrollTop(jQuery("#wdm_chat_box_gl")[0].scrollHeight);
  return false;
}
