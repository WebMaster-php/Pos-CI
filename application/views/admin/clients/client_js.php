<?php
/**
 * Included in application/views/admin/clients/client.php
 */
?>
<script>
Dropzone.options.clientAttachmentsUpload = false;
var customer_id = $('input[name="userid"]').val();

$(function(){
            initDataTable('.table-customerleads', admin_url + 'clients/sales_oppertunities/' + customer_id, '', '');
     });


$(function() {

    if ($('#client-attachments-upload').length > 0) {
        new Dropzone('#client-attachments-upload',$.extend({},_dropzone_defaults(),{
            paramName: "file",
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    window.location.reload();
                }
            }
        }));
    }

    // Save button not hidden if passed from url ?tab= we need to re-click again
    if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }

    $('a[href="#contacts"],a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').addClass('hide');
    });

    $('.profile-tabs a').not('a[href="#contacts"],a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').removeClass('hide');
    });

    $("input[name='tasks_related_to[]']").on('change', function() {
        var tasks_related_values = []
        $('#tasks_related_filter :checkbox:checked').each(function(i) {
            tasks_related_values[i] = $(this).val();
        });
        $('input[name="tasks_related_to"]').val(tasks_related_values.join());
        $('.table-rel-tasks').DataTable().ajax.reload();
    });

    var contact_id = get_url_param('contactid');
    if (contact_id) {
        contact(customer_id, contact_id);
        $('a[href="#contacts"]').click();
    }

    $('body').on('change', '.onoffswitch input.customer_file', function(event, state) {
        var invoker = $(this);
        var checked_visibility = invoker.prop('checked');
        var share_file_modal = $('#customer_file_share_file_with');
        setTimeout(function() {
            $('input[name="file_id"]').val(invoker.attr('data-id'));
            if (checked_visibility && share_file_modal.attr('data-total-contacts') > 1) {
                share_file_modal.modal('show');
            } else {
                do_share_file_contacts();
            }
        }, 200);
    });
    // If user clicked save and add new contact
    var new_contact = get_url_param('new_contact');
    if (new_contact) {
        contact(customer_id);
        $('a[href="#contacts"]').click();
    }
    $('.customer-form-submiter').on('click', function() {
        var form = $('.client-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });

    if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
        document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
            success: function(files) {
                $.post(admin_url + 'clients/add_external_attachment', {
                    files: files,
                    clientid: customer_id,
                    external: 'dropbox'
                }).done(function() {
                    window.location.reload();
                });
            },
            linkType: "preview",
            extensions: app_allowed_files.split(','),
        }));
    }

    /* Custome profile tickets table */
    var ticketsNotSortable = $('.table-tickets-single').find('th').length - 1;
    _table_api = initDataTable('.table-tickets-single', admin_url + 'tickets/index/false/' + customer_id, [ticketsNotSortable], [ticketsNotSortable], 'undefined', [$('table thead .ticket_created_column').index(), 'DESC'])
    if (_table_api) {
        _table_api.column(5).visible(false, false).columns.adjust();
    }
    /* Custome profile contacts table */
    var contractsNotSortable = $('.table-contracts-single-client').find('th').length - 1;
    _table_api = initDataTable('.table-contracts-single-client', admin_url + 'contracts/table/' + customer_id, [contractsNotSortable], [contractsNotSortable], 'undefined', [3, 'DESC']);

    /* Custome profile contacts table */
    var contactsNotSortable = $('.table-contacts').find('th').length - 1;
    initDataTable('.table-contacts', admin_url + 'clients/contacts/' + customer_id, [contactsNotSortable], [contactsNotSortable]);

    /* Customer profile invoices table */
    initDataTable('.table-invoices-single-client',
        admin_url + 'invoices/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'DESC'],
            [0, 'DESC']
        ]);

   initDataTable('.table-credit-notes', admin_url+'credit_notes/table/'+customer_id, ['undefined'], ['undefined'], undefined, [0, 'DESC']);

    /* Custome profile Estimates table */
    initDataTable('.table-estimates-single-client',
        admin_url + 'estimates/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'DESC'],
            [0, 'DESC']
        ]);



    /* Custome profile payments table */
    initDataTable('.table-payments-single-client',
        admin_url + 'payments/table/' + customer_id, [7], [7],
        'undefined', [6, 'DESC']);

    /* Custome profile reminders table */
    initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + customer_id + '/' + 'customer', [4], [4], undefined, [1, 'ASC']);

    /* Custome profile expenses table */
    initDataTable('.table-expenses-single-client',
        admin_url + 'expenses/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [5, 'DESC']);

    /* Custome profile proposals table */
    initDataTable('.table-proposals-client-profile',
        admin_url + 'proposals/proposal_relations/' + customer_id + '/customer',
        'undefined',
        'undefined',
        'undefined', [6, 'DESC']);

    /* Custome profile projects table */
    var notSortableProjects = $('.table-projects-single-client').find('th').length - 1;
    initDataTable('.table-projects-single-client', admin_url + 'projects/table/' + customer_id, [notSortableProjects], [notSortableProjects], 'undefined', <?php echo do_action('projects_table_default_order',json_encode(array(5,'ASC'))); ?>);

	
	    /* Custome profile projects table */
    var notSortableSaleopp = $('.table-saleopp-single-client').find('th').length - 1;
    initDataTable('.table-saleopp-single-client', admin_url + 'sales/table/' + customer_id, [notSortableSaleopp], [notSortableSaleopp], 'undefined', <?php echo do_action('projects_table_default_order',json_encode(array(5,'ASC'))); ?>);
	
	
    var vRules = {};
   // if (app_company_is_required == 1) {
     //   vRules = {
       //     company: 'required',
     //   }
   // }
	
	 if (app_company_is_required == 1) {
        vRules['company'] ='required';
    }
    if (app_services_are_required == 1) {
        vRules['services_in[]'] = 'required';
    } 
	
    _validate_form($('.client-form'), vRules);

    $('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
    });

    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.client-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });

});

function contact(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
	
//$('#existing_contact_save').attr('disabled',  'disabled');	
    $.post(admin_url + 'clients/contact/' + client_id + '/' + contact_id).done(function(response) {
		
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
			
		/*$.ajax({
			type:'post',
			data: {id:1},
			url:admin_url+'clients/getcompanyforcontact',
			success:function (data) {
				html_res = JSON.parse(data);
				console.log(data);
				$(html_res).insertBefore( "#contact-profile-image" );	
				$(html_res).insertBefore( "#old_contact" );	
			}
		});*/
			
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
	
		validate_contact_form();
		
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
		console.log($("#firstname").val());
		
	});

function delete_contact_profile_image(contact_id) {
    requestGet('clients/delete_contact_profile_image/'+contact_id).done(function(){
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-remove-img').addClass('hide');
        $('body').find('#contact-img').attr('src', '<?php echo base_url('assets/images/user-placeholder.jpg'); ?>');
    });
}

function validate_contact_form() {
	
	var existing_contact = $("#existing_contact").val();
		if(existing_contact == 1){
			// _validate_form('#contact-form', {
				// old_existing_account: 'required',
			// }, contactFormHandler);
			//Do Nothing
		}
		else
		{
			// var t = $('#idd').val();
				
			// if (t == '0') {
				// alert(t);
				// error = 1;
				// alert('You should select a country.');
			// }
			_validate_form('#contact-form', {
				firstname: 'required',
				lastname: 'required',
				
				// password: {
					// required: {
						// depends: function(element) {
							// var sent_set_password = $('input[name="send_set_password_email"]');
							// if ($('#contact input[name="contactid"]').val() == '' && sent_set_password.prop('checked') == false) {
								// return true;
							// }
						// }
					// }
				// },
				// email: {
					// required: false,
					// email: false,
					//Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
					// <?php if(do_action('contact_email_unique',"true") === "true"){ ?>
					// remote: {
						// url: admin_url + "misc/contact_email_exists",
						// type: 'post',
						// data: {
							// email: function() {
								// return $('#contact input[name="email"]').val();
							// },
							// userid: function() {
								// return $('body').find('input[name="contactid"]').val();
							// }
						// }
					// }
					// <?php } ?>
				// }
			}, contactFormHandler);	
		}
		 
	}

function contactFormHandler(form) {
	//console.log('kkkkkkkkkkkkkk');
    $('#contact input[name="is_primary"]').prop('disabled', false);
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
                $('#contact').modal('hide');
            }
            if(response.has_primary_contact == true){
                $('#client-show-primary-contact-wrapper').removeClass('hide');
            }
    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}

function update_all_proposal_emails_linked_to_contact(contact_id) {
    var data = {};
    data.update = true;
    data.original_email = $('body').find('#contact_update_proposals_emails').data('original-email');
    $.post(admin_url + 'clients/update_all_proposal_emails_linked_to_customer/' + contact_id, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        $('#contact').modal('hide');
    });
}

function do_share_file_contacts(edit_contacts, file_id) {
    var contacts_shared_ids = $('select[name="share_contacts_id[]"]');
    if (typeof(edit_contacts) == 'undefined' && typeof(file_id) == 'undefined') {
        var contacts_shared_ids_selected = $('select[name="share_contacts_id[]"]').val();
    } else {
        var _temp = edit_contacts.toString().split(',');
        for (var cshare_id in _temp) {
            contacts_shared_ids.find('option[value="' + _temp[cshare_id] + '"]').attr('selected', true);
        }
        contacts_shared_ids.selectpicker('refresh');
        $('input[name="file_id"]').val(file_id);
        $('#customer_file_share_file_with').modal('show');
        return;
    }
    var file_id = $('input[name="file_id"]').val();
    $.post(admin_url + 'clients/update_file_share_visibility', {
        file_id: file_id,
        share_contacts_id: contacts_shared_ids_selected,
        customer_id: $('input[name="userid"]').val()
    }).done(function() {
        window.location.reload();
    });
}

function save_longitude_and_latitude(clientid) {
    var data = {};
    data.latitude = $('#latitude').val();
    data.longitude = $('#longitude').val();
    $.post(admin_url + 'clients/save_longitude_and_latitude/'+clientid, data).done(function(response) {
       if(response == 'success') {
            alert_float('success', "<?php echo _l('updated_successfully', _l('client')); ?>");
       }
        setTimeout(function(){
            window.location.reload();
        },1200);
    }).fail(function(error) {
        alert_float('danger', error.responseText);
    });
}

function fetch_lat_long_from_google_cprofile() {
    var data = {};
    data.address = $('#long_lat_wrapper').data('address');
    data.city = $('#long_lat_wrapper').data('city');
    data.country = $('#long_lat_wrapper').data('country');
    $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
    $.post(admin_url + 'misc/fetch_address_info_gmaps', data).done(function(data) {
        data = JSON.parse(data);
        $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
        if (data.response.status == 'OK') {
            $('input[name="latitude"]').val(data.lat);
            $('input[name="longitude"]').val(data.lng);
        } else {
            if (data.response.status == 'ZERO_RESULTS') {
                alert_float('warning', "<?php echo _l('g_search_address_not_found'); ?>");
            } else {
                alert_float('danger', data.response.status);
            }
        }
    });
}
// function customer_lead_model(){
//     requestGetJSON((void 0 !== t ? t : "clients/lead/") + (void 0 !== e ? e : "")).done(function(t) {
//         _lead_init_data(t, e)
//     }).fail(function(e) {
//         alert_float("danger", e.responseText)
//     })
// }
function init_lead_customer(e) {
    $("#task-modal").is(":visible") && $("#task-modal").modal("hide"), init_lead_modal_data(e) && $("#custom_lead_modal").modal("show")
}

function validate_lead_form() {
    _validate_form($("#cust_lead_form"), {
        name: "required",
        status: {
            required: {
                depends: function(e) {
                    return !($("[lead-is-junk-or-lost]").length > 0)
                }
            }
        },
        source: "required",
        email: {
            email: !0,
            remote: {
                url: admin_url + "leads/email_exists",
                type: "post",
                data: {
                    email: function() {
                        return $('input[name="email"]').val()
                    },
                    leadid: function() {
                        return $('input[name="leadid"]').val()
                    }
                }
            }
        }
    }, lead_profile_form_handler)
}



function lead_profile_form_handler(e) {
    var t = (e = $(e)).serialize();
    $(e).serializeArray(), $("#custom_lead_modal").find('input[name="leadid"]').val();
    return $(".lead-save-btn").addClass("disabled"), $.post(e.attr("action"), t).done(function(e) {
        "" != (e = JSON.parse(e)).message && alert_float("success", e.message), e.proposal_warning && 0 != e.proposal_warning ? ($("body").find(
            "#lead_proposal_warning").removeClass("hide"), $("body").find("#custom_lead_modal").animate({
            scrollTop: 0
        }, 800)) : _lead_init_data(e, e.id), $.fn.DataTable.isDataTable(".table-leads") && table_leads.DataTable().ajax.reload(null, !1)
    }).fail(function(e) {
        return alert_float("danger", e.responseText), !1
    }), !1
}

// function update_all_proposal_emails_linked_to_lead(e) {
//     $.post(admin_url + "leads/update_all_proposal_emails_linked_to_lead/" + e, {
//         update: !0
//     }).done(function(t) {
//         (t = JSON.parse(t)).success && alert_float("success", t.message), init_lead_modal_data(e)
//     })
// }

function _lead_init_data(e, t) {
    
    var a = window.location.hash,
        i = $("#custom_lead_modal");
    if ($("#lead_reminder_modal").html(e.leadView.reminder_data), i.find(".customer_data").html(e.leadView.data), i.modal({
            show: !0,
            backdrop: "static"
        }), init_tags_inputs(), init_selectpicker(), init_form_reminder(), init_datepicker(), init_color_pickers(), validate_lead_form(), "#tab_lead_profile" !=
        a && "#attachments" != a && "#lead_notes" != a || (window.location.hash = a), "" != t && void 0 !== t) {
        "undefined" != typeof Dropbox && document.getElementById("dropbox-chooser-lead").appendChild(Dropbox.createChooseButton({
            success: function(e) {
                $.post(admin_url + "leads/add_external_attachment", {
                    files: e,
                    lead_id: t,
                    external: "dropbox"
                }).done(function() {
                    init_lead_modal_data(t)
                })
            },
            linkType: "preview",
            extensions: app_allowed_files.split(",")
        })), "undefined" != typeof leadAttachmentsDropzone && leadAttachmentsDropzone.destroy(), leadAttachmentsDropzone = new Dropzone(
            "#lead-attachment-upload", $.extend({}, _dropzone_defaults(), {
                sending: function(e, a, i) {
                    i.append("id", t), 0 == this.getQueuedFiles().length && i.append("last_file", !0)
                },
                success: function(e, t) {
                    t = JSON.parse(t), 0 === this.getUploadingFiles().length && 0 === this.getQueuedFiles().length && _lead_init_data(t, t.id)
                }
            })), i.find('.nav-tabs a[href="' + window.location.hash + '"]').tab("show");
        var n = i.find("#lead_activity .feed-item:last-child .text").html();
        void 0 !== n ? i.find("#lead-latest-activity").html(n) : i.find(".lead-latest-activity > .lead-info-heading").addClass("hide")
    }
}

function init_lead_modal_data(e, t) {
    if(e=='undefine'){
     e=0;  
    }else{
        e=e;
    }
    if(t=='undefine'){
    var client_id=$('#client_id').val();
    }else{
    var client_id=$('#client_id').val();
    }
    
    requestGetJSON((void 0 !== t ? t : "clients/lead/"+e+'/'+client_id) + (void 0 !== e ? e : "")).done(function(t) {
        _lead_init_data(t, e)
    }).fail(function(e) {
        alert_float("danger", e.responseText)
    })
	$.ajax({
        type:'POST',
        url:'<?php echo base_url();?>admin/clients/get_custom_client_data',
        data:{val:client_id},
        success: function(res){
         var data = $.parseJSON(res);
           if($('#name').val() == '') {
				$('#name').val(data.company);
			}
            if($('#zip').val() == '') {
				$('#zip').val(data.zip);
			}
            if($('#email').val() == '') {
				$('#email').val(data.email)
			}
			if($('#website').val() == '') {
				$('#website').val(data.website);
			}
			if($('#phonenumber').val() == '') {
				$('#phonenumber').val(data.phonenumber);
			}
			if($('#address').val() == '') {
				$('#address').val(data.address);
			}
			if($('#description').val() == '') {
				$('#description').val(data.description);
			}
			if($('#city').val() == '') {
				$('#city').val(data.city);
			}
			if($('#state').val() == '') {
				$('#state').val(data.state);
			}
        }
    });
}
</script>
