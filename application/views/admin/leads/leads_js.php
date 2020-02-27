
<script>

function validate_lead_contact_form() {
  
  var existing_contact = $("#existing_contact").val();
    if(existing_contact == 1){
    }
    else
    {

      _validate_form('#lead_contact_form', {
        first_name: 'required',
        last_name: 'required',
      }, contactFormHandler); 
    }
     
  }
  function lead_contact(client_id, contact_id, lead_type) {
	
    if (typeof(contact_id) == 'undefined') {
      contact_id = '';
    }
//$('#existing_contact_save').attr('disabled',  'disabled');
	  var editUrl = admin_url + 'leads/contact/' + client_id + '/' + contact_id;
	  if(lead_type == 1) {
	  		var  editUrl = admin_url + 'leads/contact/' + client_id + '/' + contact_id;
	  } 
	  if(lead_type == 0) {
	  		var  editUrl = admin_url + 'clients/costumer_contact/' + client_id + '/' + contact_id;
	  } 
	  //if(lead_type == 0) {
	  		//var  editUrl = admin_url + 'clients/contact/' + client_id + '/' + contact_id;
	  //} 
$.post(editUrl).done(function(response) {
// alert('here');
//return false;
  $('#lead-modal').hide();
  $('#lead_contact_data').show();
  $('#lead_contact_data').html(response);
  $('#lead_contacts').modal({
    show: true,
    backdrop: 'static'
  });
  $('body').off('shown.bs.modal','#lead_contacts');
  $('body').on('shown.bs.modal', '#lead_contacts', function() {

    if (contact_id == '') {
      $('#lead_contacts').find('input[name="firstname"]').focus();
    }
  });
  init_selectpicker();
  init_datepicker();
  custom_fields_hyperlink();
  
  validate_lead_contact_form();

}).fail(function(error) {
  var response = JSON.parse(error.responseText);
  alert_float('danger', response.message);
})
}
$('body').on('click', '#existing_contact', function(){
    exiscontacttogle = $("#existing_contact").val();
    if(exiscontacttogle == 1)
    {
      $("#existing_contact").val('0');
      $("#old_existing_account").removeClass("required");
      $("#idd_1").removeClass("required");
      $("#idd").addClass("required");
      $("#firstname").val("");
      $("#lastname").val("");
      $("#email").val("");
      $("#password").val("");
      $("#old_contact").hide();
      $("#new_contact").show();
      $('#existing_contact_save').removeAttr("disabled");
      $("#check").hide();
    }
    else
    {
      $("#idd").removeClass("required");
      $("#old_existing_account").addClass("required");
      $("#idd_1").addClass("required");
      $("#firstname").attr("aria-invalid","false");
      $("#firstname").val("AA");
      $("#lastname").attr("aria-invalid","false");
      $("#lastname").val("AA");
      $("#email").attr("aria-invalid","false");
      $("#email").val("aa@mail.com");
      $("#password").val("AA");
      $("#existing_contact").val('1');
      $("#new_contact").hide();
      $("#old_contact").css({'display':'inline-block', 'width':'100%'});    
    }
    //console.log($("#firstname").val());
    
  });
//customer contact toggle
$('#customers_records').hide();
$('body').on('click', '#existing_customer', function(){
  exiscustomertoggle = $("#existing_customer").val();
  if(exiscustomertoggle==1){
$("#existing_customer").val('0');
//$('#source').prop('disabled',false);
$('#customers_records').hide();
$("#new_company").show();
$('input[name=name]').val('');
$('input[name=city]').val('');
$('input[name=state]').val('');
$('input[name=address]').val('');
$('input[name=website]').val('');
$('input[name=zip]').val('');
  }else{

$("#existing_customer").val('1');
//$('#source').empty();
//$('#source').prop('disabled',true);
$('#customers_records').show();
$("#new_company").hide();
  }

});

 $('body').on('change', '#old_existing_customer', function() {
    var customer_userid = $(this).val(); 
    //var company_id = $('#company_id').val();
   // alert(customer_userid);
     $.ajax({
        type:'POST',
        dataType: 'json',
        url:'<?php echo base_url(); ?>admin/leads/get_existing_customer',
        data:{'userid':customer_userid},
        success:function(data){
         // var customerData=JSON.parse(data);
            if(data.already_lead==1){
            $("#check_error").show();
            }else{
            $("#check_error").hide();
            $('input[name=name]').val(data.company);
            $('input[name=city]').val(data.city);
            $('input[name=state]').val(data.state);
            $('input[name=phonenumber]').val(data.phonenumber);
            $('textarea[name=address]').val(data.address);
            $('input[name=website]').val(data.website);
            $('input[name=zip]').val(data.zip);
            }
        } 
      });
   });



  function contactFormHandler(form) {
 
    $('#lead_contact_form input[name="is_primary"]').prop('disabled', false);
    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response){
             response = JSON.parse(response);
            if (response.success) {
        location.reload();
                alert_float('success', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }
            }else{
        alert_float('danger', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }
      }
            if ($.fn.DataTable.isDataTable('.table-contacts')) {
                $('.table-contacts').DataTable().ajax.reload(null,false);
            }
            if (response.proposal_warning && response.proposal_warning != false) {
                $('body').find('#contact_proposal_warning').removeClass('hide');
                $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
                $('#contact').animate({
                    scrollTop: 0
                }, 800);
            } else {
                $('#lead_contact_form').modal('hide');
            }
            if(response.has_primary_contact == true){
                $('#client-show-primary-contact-wrapper').removeClass('hide');
            }
    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}

 $('body').on('hidden.bs.modal', '#lead_contacts', function() {
  $('#lead-modal').show();
  $('#tab_lead_contacts').show();
  });
</script>