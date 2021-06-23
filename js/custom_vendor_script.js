jQuery(document).ready(function(){
	console.log("calleeeeeeeedddddddddddddddddddddddd");
	jQuery(".product-info-tab-wrapper").on("change",".collection_id",function(){
		if(jQuery(this).val() == "new")
		{
			jQuery(this).parents("#collection_tab").find(".add_new_collection").show();	
		}
		else
		{
			jQuery(this).parents("#collection_tab").find(".add_new_collection").hide();	
		}
	});
	jQuery("#_is_original").click(function(){
		if(jQuery(this).is(":checked"))
		{
			//jQuery("#_manage_stock").prop("checked",false);
			//jQuery("#_stock").val("1");
			jQuery(".stock_fields").hide();	
		}
	})
	
	
	/* limit color checkbox */
	jQuery('.taxonomy-widget.color').find("input[type='checkbox']").on('change', function(){
		if(jQuery('input[name="tax_input[color][]"]:checked').length > 3) {
			this.checked = false;
		}
	});
	
	/* limit style checkbox */
	jQuery('.taxonomy-widget.style').find("input[type='checkbox']").on('change', function(){
		if(jQuery('input[name="tax_input[style][]"]:checked').length > 1) {
			this.checked = false;
		}
	});
	
	/* limit subject checkbox */
	jQuery('.taxonomy-widget.subject').find("input[type='checkbox']").on('change', function(){
		if(jQuery('input[name="tax_input[subject][]"]:checked').length > 1) {
			this.checked = false;
		}
	});
	
	/* limit types checkbox */
	jQuery('.taxonomy-widget.types').on('change',"input[type='checkbox']", function(){
		//console.log(jQuery('input[name="tax_input[types][]"]:checked').length);
		if(jQuery('input[name="tax_input[types][]"]:checked').length > 1) {
			this.checked = false;
		}
	});

	/* limit medium checkbox */
	jQuery('.taxonomy-widget.medium').on('change',"input[type='checkbox']", function(){
		//console.log(jQuery('input[name="tax_input[types][]"]:checked').length);
		if(jQuery('input[name="tax_input[medium][]"]:checked').length > 1) {
			this.checked = false;
		}
	});
	/* limit orientation checkbox */
	jQuery('.taxonomy-widget.orientation').find("input[type='checkbox']").on('change', function(){
		if(jQuery('input[name="tax_input[orientation][]"]:checked').length > 1) {
			this.checked = false;
		}
	});
	
	/* limit size checkbox */
	jQuery('.taxonomy-widget.size').find("input[type='checkbox']").on('change', function(){
		if(jQuery('input[name="tax_input[size][]"]:checked').length > 1) {
			this.checked = false;
		}
	});
	
	/* filter medium and types according to product category */
	jQuery('input[name="tax_input[product_cat][]"]').click(function(e){
		//console.log("called");
		if(jQuery(this).is(":checked"))
		{
			var parentid = jQuery(this).val();
			var product_id = jQuery("#product_id").val();
			//console.log(parentid);
			jQuery(".medium_sec").show();
			jQuery.ajax({ 
				url: ec_ajax_url, 
				data: "action=get_taxonomy_child&parentid="+parentid+"&type=medium&product_id="+product_id, 
				type  : 'POST',
				//async : false,
				success: function(result) {
					console.log(result);
					result = JSON.parse( result );
					if(result.success==1)
					{
						var mData = result.data;
						var sel = result.selected;
						var str="";
						jQuery(mData).each(function () {
							var mid = parseInt(this.child_id);
							var cursel = jQuery.inArray(mid,sel);
							var chk ="";
							if(cursel >= 0 )
							{
								chk = "checked='checked'";
							}
							str += '<li><label><input type="checkbox" name="tax_input[medium][]" value="'+this.child_id+'" '+chk+'> '+this.name+' </label></li>';
						});
						if(str!='')
						{
							jQuery(".taxonomy-widget.medium").html(str);
						}
					}
					jQuery(".medium_sec").show();
				}
			});
			jQuery(".types_sec").hide();
			jQuery.ajax({ 
				url: ec_ajax_url, 
				data: "action=get_taxonomy_child&parentid="+parentid+"&type=types&product_id="+product_id, 
				type  : 'POST',
				//async : false,
				success: function(result) {
					result = JSON.parse( result );
					if(result.success==1)
					{
						var mData = result.data;
						var sel = result.selected;
						var str="";
						
						jQuery(mData).each(function () {
							var chk ="";
							var mid = parseInt(this.child_id);
							var cursel = jQuery.inArray(mid,sel);
							var chk ="";
							if(cursel >= 0 )
							{
								chk = "checked='checked'";
							}
							str += '<li><label><input type="checkbox" name="tax_input[types][]" value="'+this.child_id+'" '+chk+'> '+this.name+' </label></li>';
						});
						if(str!='')
						{
							jQuery(".taxonomy-widget.types").html(str);
							
						}
						
					}
					jQuery(".types_sec").show();
				}
			});	
		}	
	});
	
	jQuery(window).load(function(){
		if(jQuery("input[name='tax_input[product_cat][]']:checked").val()== undefined || jQuery("input[name='tax_input[product_cat][]']:checked").val()== "")
		{
			jQuery(".medium_sec").hide();
			jQuery(".types_sec").hide();
		}	
		else if(jQuery("input[name='tax_input[product_cat][]']:checked").val() > 0)
		{
			jQuery('input[name="tax_input[product_cat][]"]:checked').trigger("click");	
		}
	});
	
	jQuery(".shipping_type").on("change",function(e){
		if(jQuery(this).val()=="flat_rate")
		{
			jQuery(".shipping_price_sec").show();
		}
		else
			jQuery(".shipping_price_sec").hide();
	});
	
	if(jQuery("#wcmp-edit-product-form").length)
	{
		console.log("saadasd");
		jQuery("#wcmp-edit-product-form").validate().settings.ignore = "";
		
	}
	
	
	
	jQuery(".is_orig").click(function(e){
		console.log(jQuery(this).val());
		if(jQuery(this).val()=="yes")
		{
			jQuery("#_manage_stock").prop("checked",true);
			jQuery(".stock_fields").show();
			jQuery("#_manage_stock").attr("disabled",true);
			jQuery("#_stock").val("1");
			//jQuery("#_original_stock").val("1");
			jQuery("#_stock").attr("readonly",true)
		}
		else
		{
			//jQuery("#_manage_stock").prop("checked",true);
			jQuery("#_manage_stock").attr("disabled",false);
			jQuery("#_stock").attr("readonly",false);
			console.log("dsdsdsd");
		}
	});
	
	
	jQuery("#wcmp-edit-product-form").submit(function(e){
		//e.preventDefault();
		var err = 0;
		jQuery(".taxo_error").html("").hide();
		if(jQuery(".shipping_type").val()=="flat_rate")
		{
			if(jQuery(".shipping_price").val()=="")
			{
				jQuery(".shipping_price").parents(".shipping_price_div").append("<label class='error'>This field is required.</div>");
				err++;
			}	
			else
			{
				jQuery(".shipping_price").parents(".shipping_price_div").find("error").remove();
			}
		}
		
		if(jQuery("input[name='tax_input[product_cat][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[product_cat][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		if(jQuery("input[name='tax_input[medium][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[medium][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		if(jQuery("input[name='tax_input[subject][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[subject][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		if(jQuery("input[name='tax_input[style][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[style][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		
		if(jQuery("input[name='tax_input[types][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[types][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		if(jQuery("input[name='tax_input[color][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[color][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		if(jQuery("input[name='tax_input[size][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[size][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
		if(jQuery("input[name='tax_input[orientation][]']:checked").length == 0)
		{
			jQuery("input[name='tax_input[orientation][]']").parents(".taxo_sec").find(".panel-heading").find(".taxo_error").html("This field is required").show();
			err++;
		}
				
		if(err > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	});
});

jQuery("#wcmp_afm_product_submit").click(function(){
	console.log("custom.js called");
       
    var arttitle = jQuery("input[name='post_title']").val();
    var artdec = jQuery("textarea[name='product_description']").val();
    var product_excerpt = jQuery("textarea[name='product_excerpt']").val();
    var regular_price = jQuery("input[name='_regular_price']").val();
    
    var wei = jQuery("input[name='_weight']").val();
    var height = jQuery("input[name='_height']").val();
    var wid = jQuery("input[name='_width']").val();
    var leng = jQuery("input[name='_length']").val();
    var shipping_typeee = jQuery("#shipping_type");
    
   
    
    if(arttitle == ''){
        alert("Oops! You missed something; [Go to Artwork Title] ");
      return false;
    }else{
      if(arttitle.length > 50){
          alert("Sorry, the Artwork Title must be less than 50 characters long. Yours is " + arttitle.length);
        return false;
      }
      
    }
      
     if(artdec == ''){
        alert("Oops! You missed something; [Go to Artwork Description]");
       return false;
    }
    
         
      if(jQuery(".upload_image_id").val() == ""){
      alert("Oops! You missed something; [Go to Click to upload Image]");
      return false;
    } 
    
      if(product_excerpt == ''){
        alert("Oops! You missed something; [Go to Material/Medium] ");
       return false;
    }
         if(regular_price == ''){
        alert("Oops! You missed something; [Go to (General) Retail Price($)] ");
       return false;
    }
     var is_original = jQuery('input:radio[name="_is_original"]:checked').val();
    
      if(typeof is_original == "undefined" || jQuery('input:radio[name="_is_original"]:checked').val() == ""){
       alert("Oops! You missed something; [Go to (Inventory) Is this an Original Piece]");
      return false;
    }

    
    

     if(wei == ''){
        alert("Oops! You missed something; [Go to (Shipping)  ̣Weight (lbs)] ");
       return false;
    }
       if(height == ''){
        alert("Oops! You missed something; [Go to (Shipping) Height]");
       return false;
    }

       if(wid == ''){
        alert("Oops! You missed something; [Go to (Shipping) Width]");
       return false;
    }
       if(leng == ''){
        alert("Oops! You missed something; [Go to (Shipping) Depth]");
       return false;
    }

        if (shipping_typeee.val() == "") {
        alert("Oops! You missed something; [Go to (Shipping) Shipping Method!] ");
        return false;
    } 
    
    
   if(jQuery(".product_cat").find('input[type="radio"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Main Artwork Category]");
      return false;
    }  
    
    if(jQuery(".medium").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Medium Category]");
      return false;
    }  
     if(jQuery(".subject").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Subject Category]");
      return false;
    } 
    
       if(jQuery(".style").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Style Category]");
      return false;
    }
    
        if(jQuery(".types").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Type Category]");
      return false;
    }  
       if(jQuery(".color").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to color Category]");
      return false;
    }  
    
       if(jQuery(".size").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Size Category]");
      return false;
    }  
    
         if(jQuery(".orientation").find('input[type="checkbox"]:checked').length == 0){
      alert("Oops! You missed something; [Go to Orientation Category] ");
      return false;
    }  
   
       if(jQuery('.product_tag ').find(':selected').length == 0)
    {
      alert("Oops! You missed something; [Go to Artwork Tags (Keywords)]");
      return false;
    }  
    
    

    
  });
