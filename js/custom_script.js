

$ = jQuery;
$(document).ready(function(){
	//console.log("js called");
	if($('.ec_date').length)
	{
		$('.ec_date').datepicker({
			dateFormat:'yy-mm-dd'
		});
	}
	$(".request_qty").on("keyup",function(e){
		
		if($(".request_qty").val() > 0 && $(".actual_price").html() > 0)
		{
			var client_total = parseInt($(".request_qty").val()) * parseInt($(".actual_price").html());
			$(".client_total").html(client_total);
			$(".client_total_sec").show();
		}
		
		if($(".request_qty").val() > 0 && $(".actual_price").html() > 0)
		{
			var designer_total = parseInt($(".request_qty").val()) * parseInt($(".designer_price").html());
			$(".designer_total").html(designer_total);
			$(".designer_total_sec").show();
		}
		
		
	});
	
	$(".tab_link").click(function(e){
		e.preventDefault();
		//$(".statement_info").hide();
		var id = $(this).attr("href");
		$(".tab_link").removeClass("active");
		$(".tab_content").removeClass("active");
		$(this).addClass("active");
		$(id).addClass("active");
	});
	
	$(".view_statement").click(function(e){
		e.preventDefault();
		var id = $(this).attr("href");
		$(".tab_link").removeClass("active");
		$(".tab_content").removeClass("active");
		$("#vendor_info").addClass("active");
		$(".statement_info").show();
		$("[href='#vendor_info']").addClass("active");
	});
	
	$(".ec_request_quote").click(function(e){
		e.preventDefault();
		$(".ec_overlap").show();
		$(".ec_popup").show();
	});
	
	$(".ec_close_me").click(function(e){
		e.preventDefault();	
		$(".ec_overlap").hide();
		$(".ec_popup").hide();
	});
	
	$(".frmrequestquote").submit(function(e){
		e.preventDefault();
		$(".ec_error").html("").hide();
		var formData = $(this).serialize();
		console.log(formData);
		jQuery.ajax({ 
				url: ec_ajax.ec_ajax_url, 
				data: "action=add_request_quote_info&"+formData, 
				type  : 'POST',
				//async : false,
				success: function(result) {
					console.log(result);
					if(result > 0)
					{
						location.reload();	
					}
					else
					{
						$(".ec_error").html(result).show();	
					}
					
					//result = JSON.parse( result );
					/*if(result.success==1)
					{
					 }
						*/
				}
			});	
	});
	
	if($(".frmrequestquote").length)
	{
		$(".frmrequestquote").validate();
	}
	
	jQuery(".woof_container_inner").on("click","input[type='radio'][name='medium']",function(e){
		if($(this).is(":checked"))
		{
			var slug = jQuery(this).attr("data-slug");
			woof_current_values.medium = slug;
			
			jQuery("input[type='hidden'][name='medium']").val(slug);	
			jQuery(this).parents("ul").find("a").removeClass("woof_radio_term_reset_visible");
			jQuery(this).parents("li").find("a").addClass("woof_radio_term_reset_visible");
		}
	});
	
/*	jQuery("body").on("click",".woof_radio_term_reset_visible",function(e){
		e.preventDefault();
		var name = jQuery(this).parents("li").find("input[type='radio']:checked").attr("name");
		if(name=="medium")
			woof_current_values.medium ="";
		else if(name=="types")
			woof_current_values.types ="";
		jQuery(this).parents("li").find("input[type='radio']:checked").attr("checked",false);
		jQuery(this).parents("li").find("a").removeClass("woof_radio_term_reset_visible");
	})
*/
	jQuery(".woof_container_inner").on("click","input[type='radio'][name='types']",function(e){
		if($(this).is(":checked"))
		{
			var slug = jQuery(this).attr("data-slug");
			woof_current_values.types = slug;
			jQuery(this).parents("ul").find("a").removeClass("woof_radio_term_reset_visible");
			jQuery(this).parents("li").find("a").addClass("woof_radio_term_reset_visible");
		}
	});
	$(".woof_select_product_cat").on("change",function(e){
		e.preventDefault();	
		if($(this).val() !='')
		{
			get_data_by_cat($(this).val());
		}
	});
	
	function get_data_by_cat(catid)
	{
		var mselid = jQuery("input[name='medium']:checked").val();
		jQuery.ajax({ 
			url: ec_ajax.ec_ajax_url, 
			data: "action=get_medium_type_by_category_name&cat="+catid+"&type=medium", 
			type  : 'POST',
			//async : false,
			success: function(result) {
				result = JSON.parse( result );
				if(result.success==1)
				{
					var mData = result.data;
					var str ="";
					jQuery(mData).each(function () {
						var msel ="";
						var cls="";
						if(mselid == this.child_id)
						{
							 msel ="checked='checked'";
							 cls=" woof_radio_term_reset_visible";
						 }
						
						str +='<li class="woof_term_'+this.child_id+'"><input type="radio" id="woof_'+this.child_id+'" class="woof_radio_term woof_radio_term_'+this.child_id+'" data-slug="'+this.slug+'" data-term-id="'+this.child_id+'" name="medium" value="'+this.child_id+'" '+msel+'>';
						str +='<label class="woof_radio_label " for="woof_'+this.child_id+'">'+this.name+'</label>';
						str +='<a href="#" data-name="medium" data-term-id="'+this.child_id+'" style="display: none;" class="woof_radio_term_reset '+cls+' woof_radio_term_reset_'+this.child_id+'"><img src="'+ec_ajax.ec_site_url+'/wp-content/plugins/woocommerce-products-filter/img/delete.png" height="12" width="12" alt="Delete"></a>';
						str +='<input type="hidden" value="'+this.name+'" data-anchor="woof_n_medium_'+this.slug+'">';
						str +='</li>';
						
					});
					if(str!='')
					{
						jQuery(".woof_container_inner_medium").find("ul").html(str);
						var slug = jQuery("input[name='medium']:checked").attr("data-slug");
						jQuery("input[type='hidden'][name='medium']").val(slug);	
						//jQuery(".woof_container_inner").find("form").append("<input type='hidden' id='filter_medium' name='medium' value='"+slug+"' />");
						$(".woof_container_medium").show();	
					}
					
				}

			}
		});
		
		var tselid = jQuery("input[name='types']:checked").val();
		jQuery.ajax({ 
			url: ec_ajax.ec_ajax_url, 
			data: "action=get_medium_type_by_category_name&cat="+catid+"&type=types", 
			type  : 'POST',
			//async : false,
			success: function(result) {
				//console.log(result);
				//console.log(result);
				result = JSON.parse( result );
				if(result.success==1)
				{
					var mData = result.data;
					var str ="";
					jQuery(mData).each(function () {
						var tsel ="";
						var cls="";
						if(tselid  == this.child_id)
						{
							tsel = "checked='checked'";
							cls=" woof_radio_term_reset_visible";
						}
						str +='<li class="woof_term_'+this.child_id+'"><input type="radio" id="woof_'+this.child_id+'" class="woof_radio_term woof_radio_term_'+this.child_id+'" data-slug="'+this.slug+'" data-term-id="'+this.child_id+'" name="types" value="'+this.child_id+'" '+tsel+'>';
						str +='<label class="woof_radio_label " for="woof_'+this.child_id+'">'+this.name+'</label>';
						str +='<a href="#" data-name="types" data-term-id="'+this.child_id+'" style="display: none;" class="woof_radio_term_reset '+cls+' woof_radio_term_reset_'+this.child_id+'"><img src="'+ec_ajax.ec_site_url+'/wp-content/plugins/woocommerce-products-filter/img/delete.png" height="12" width="12" alt="Delete"></a>';
						str +='<input type="hidden" value="'+this.name+'" data-anchor="woof_n_types_'+this.slug+'">';
						str +='</li>';
						
					});
					if(str!='')
					{
						jQuery(".woof_container_inner_type").find("ul").html(str);	
						var slug = jQuery("input[name='types']:checked").attr("data-slug");
						$(".woof_container_types").show();
					}
				}
			}
		});
	
	}
	
	jQuery(window).load(function(){
		jQuery(".widget-woof").prepend('<div class="ec-search-sec"><label>Search Artwork</label><button class="button btnecsearch" type="button">Filter</button></div>');


		if($(".woof_select_product_cat").length && $(".woof_select_product_cat").val()!='')
		{
			/* change product categories to categories text in search */
			jQuery(".woof_container_inner_productcategories").find("h4").html("Categories");
			if(jQuery(".woof_container_inner_productcategories").find(".chosen-single").find("span").text() == "Product categories")
			{
				jQuery(".woof_container_inner_productcategories").find(".chosen-single").html("<span>Categories</span>");	
			}
			$(".woof_select_product_cat").trigger("change");	
			$(".woof_submit_search_form").attr("type","button");
		}
		else
		{
			var catid = woof_really_curr_tax.term_id;
			if(catid > 0)
			{
				console.log("called");
				get_data_by_cat(catid);
			}
		}	
		
		
	});
	$('#ec_registration_form').submit(function(e){
		e.preventDefault();
		e.stopPropagation();
		console.log("called");
		var ele = $(this);
		ele.find(".pwd_error").remove();
		var regularExpression = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
		var pwd = ele.find('input[type="password"]').val();
		var ans = regularExpression.test(pwd);
		console.log(ans);		
		if(ans)
		{
			return true;
		}
		else 
		{
			e.stopImmediatePropagation();
			ele.append("<div class='pwd_error'>Check that your password is a minimum of 8 characters and contains at least;. <ul><li>One number</li><li>One uppercase letter</li><li>One lowercase letter</li><li>One special character, like a comma or dollar sign</li></ul></div>");
			var top = ele.find(".pwd_error").offset().top;
			top = top-100;
			$('html, body').animate({ scrollTop: top}, 1000);
			return false;
		}
	});
	
	jQuery(".widget-woof").on("click",".btnecsearch",function(){
		jQuery(".woof_submit_search_form_container").find("button").trigger("click");
	});
	
});
