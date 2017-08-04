/**********************************************************************************************************************************************/
/*											DOCUMENT READY											*/
/**********************************************************************************************************************************************/
jQuery( document ).ready(function() { 

	jQuery('.profile-image-block .img-block .display-icon, .profile-image-block .img-block .img-circle').click(function(){
		jQuery('input[type=file]#edit-user-picture-upload').click();
		return false;
	});
	
	// jQuery( "#datepicker" ).datepicker({
	// 	dateFormat: 'dd/mm/yy' 
	// });
	 
	  jQuery(".form-radio").each(function() {
	 	 jQuery(this).prependTo(jQuery(this).parent().parent()); 
	  });
	
	jQuery(".js-form-item-field-description").addClass("form-group");
	jQuery(".profile-image-block, .profile-image-block div").removeClass("form-group");
	jQuery("#edit-upload-details #ajax-wrapper--2, #edit-upload-details .form-type-managed-file").removeClass("form-group");
	
	 jQuery( ".form-type-checkbox .control-label" ).each(function() {
		 jQuery(this).addClass("checkbox-default primary");
		 jQuery(this).wrapInner( "<span></span>");
		 jQuery(this).prependTo(jQuery(this)); 
	 });
	 
	 jQuery( ".form-type-checkbox .control-label .form-checkbox" ).each(function() {
		 jQuery(this).prependTo(jQuery(this).parent().parent()); 
	 });
	
	jQuery("#edit-upload-details, #edit-birthday, #edit-phone-number, #edit-refer-promoters, #edit-view-users").removeClass("panel panel-default");
	jQuery("#edit-birthday > div, #edit-phone-number > div, #edit-refer-promoters > div, #edit-view-users > div").removeClass("panel-heading");
	jQuery("#edit-birthday > div, #edit-phone-number > div, #edit-refer-promoters > div, #edit-view-users > div").removeClass("panel-body");
	
	var formid=jQuery('.introduce-forms-introduce-form').attr("id");
	if(formid !=='introduce-forms-introduce-form'){
		jQuery('.introduce-forms-introduce-form').attr("id",'introduce-forms-introduce-form');
		jQuery('.introduce-forms-introduce-form .profile-image-block #ajax-wrapper').addClass('form-group');
		jQuery('.introduce-forms-introduce-form #edit-user-picture-upload').show();
	}
	var old_href = jQuery('#edit-thumbnail-preview img').attr('src');
	jQuery('#edit-thumbnail-preview img').attr("data",old_href);
});

/**********************************************************************************************************************************************/
/*											AJAX COMPLETE											*/
/**********************************************************************************************************************************************/

jQuery( document ).ajaxComplete(function() {

	jQuery('.profile-image-block .form-managed-file.js-form-managed-file').attr("id",'edit-user-picture-upload');
	jQuery('.profile-image-block .form-managed-file.js-form-managed-file input').attr("id",'edit-user-picture-upload');
	var fillls = jQuery("input[name='user-picture[fids]']").val();
	if(fillls == ''){
		jQuery('.introduce-forms-introduce-form #edit-user-picture-upload').parent().parent().hide();
		var href = jQuery('#edit-thumbnail-preview img').attr('data');
		jQuery('#edit-thumbnail-preview img').attr("src",href);
	}
	else{
		var href = jQuery('#edit-user-picture-upload .file-link a').attr('href');
		//~ jQuery('#edit-user-picture-upload .file-icon img').attr("src",href);
		//~ jQuery('#edit-user-picture-upload .file-icon img').attr("width","50px");
		jQuery('#edit-user-picture-upload .file-icon img').hide();
		jQuery('#edit-thumbnail-preview img').attr("src",href);
		
	}
});

