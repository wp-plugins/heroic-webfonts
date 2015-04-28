/**
* HT Webfonts - customizer live functionality
*/

jQuery(document).ready(function($) {

	var totalCount = htWebfonts.fontCount.count;
	var variants = htWebfonts.variants;
	var i18n = htWebfonts.i18n;
	var defaults = htWebfonts.defaults;
	console.log('webfonts-scripts loaded');
	

	//loop through the max number of permittable webfont controls
	for (var i = 0; i < 400; i++) {	

		//test, continue if item does not exist
		var item = $('#customize-control-ht_webfont_name_'+i);
		if(item.length==0){
			continue;
		}
		
		//set links data-index attribute
		$( '#customize-control-ht_webfont_title_'+i+' a' ).attr('data-index', i);

		//convert font size to slider
		var webfontSizeSliderHTML = '<div id="webfont-size-'+i+'-slider"></div>';
		var initialValue =   $( '#customize-control-ht_webfont_size_'+i+' input' ).val();
		$( '#customize-control-ht_webfont_size_'+i+' input' ).attr('data-index', i);
		$( '#customize-control-ht_webfont_size_'+i+' label' ).append(webfontSizeSliderHTML);
		$( '#webfont-size-'+i+'-slider' ).slider({	max: 80, 
													min: 8, 
													step: 1, 
													value: initialValue,
													change: function( event, ui ) { sliderUpdate(event, ui); }
		});

		//convert line height to slider
		var webfontHeightSliderHTML = '<div id="webfont-height-'+i+'-slider"></div>';
		var initialValue = $( '#customize-control-ht_webfont_height_'+i+' input' ).val();
		$( '#customize-control-ht_webfont_height_'+i+' input' ).attr('data-index', i);
		$( '#customize-control-ht_webfont_height_'+i+' label' ).append(webfontHeightSliderHTML);
		$( '#webfont-height-'+i+'-slider' ).slider({max: 30, 
													min: 3, 
													step: 1, 
													value: initialValue,
													change: function( event, ui ) { sliderUpdate(event, ui); }
		});

		//convert font space to slider
		var webfontSpacingSliderHTML = '<div id="webfont-spacing-'+i+'-slider"></div>';
		var initialValue = $( '#customize-control-ht_webfont_spacing_'+i+' input' ).val();
		$( '#customize-control-ht_webfont_spacing_'+i+' input' ).attr('data-index', i);
		$( '#customize-control-ht_webfont_spacing_'+i+' label' ).append(webfontSpacingSliderHTML);
		$( '#webfont-spacing-'+i+'-slider' ).slider({	max: 5, 
													min: -5, 
													step: 1, 
													value: initialValue,
													change: function( event, ui ) { sliderUpdate(event, ui); }
		});

		//triggered when a slider is moved and value changed
		function sliderUpdate(event, ui){
		 	var target = event.target;
		 	var input = $(target).prev('input');
		 	var index = input.attr('data-index');
		 	//get the wp customize link
		 	var link = input.attr('data-customize-setting-link');
		 	input.val(ui.value);
		 	//updateValues(index);
		 	wp.customize.value(link)(ui.value);
													 
		}	

		//selector
		wp.customize( 'ht_webfont_selector_'+i, function( value ) {
				var index = i;
				value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//family
		wp.customize( 'ht_webfont_family_'+i, function( value ) {
				var index = i;
				value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//style
		wp.customize( 'ht_webfont_style_'+i, function( value ) {
				var index = i;
					value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//color
		wp.customize( 'ht_webfont_color_'+i, function( value ) {
				var index = i;
				value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//font size
		wp.customize( 'ht_webfont_size_'+i, function( value ) {
				var index = i;
				value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//font height
		wp.customize( 'ht_webfont_height_'+i, function( value ) {
				var index = i;
				value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//font spacing
		wp.customize( 'ht_webfont_spacing_'+i, function( value ) {
				var index = i;
				value.bind(function( newval ) {
					updateValues(index);	
				} );
			} );

		//set control visibility
		setControlVisibility(i);

	}

	//sets the visibility of the controls
	function setControlVisibility(index){
		try{
			//fonts object
			var itemDefault = defaults[index];


			var name = $('#customize-control-ht_webfont_name_'+index+' input');
			setInputVisibility(name, itemDefault['name-visibility']);
			//selector
			var selector = $('#customize-control-ht_webfont_selector_'+index+' input');
			setInputVisibility(selector, itemDefault['selector-visibility']);
			//font source
			var source = $('#customize-control-ht_webfont_source_'+index+' select');
			setInputVisibility(source, itemDefault['source-visibility']);
			//font family
			var family = $('#customize-control-ht_webfont_family_'+index+' select');
			setInputVisibility(family, itemDefault['family-visibility']);
			//style
			var style = $('#customize-control-ht_webfont_style_'+index+' select');
			setInputVisibility(style, itemDefault['style-visibility']);
			//color
			var color = $('#customize-control-ht_webfont_color_'+index+'  input');
			setInputVisibility(color, itemDefault['color-visibility']);
			//size
			var size = $('#customize-control-ht_webfont_size_'+index+'  input');
			setInputVisibility(size, itemDefault['size-visibility']);
			//height
			var height = $('#customize-control-ht_webfont_height_'+index+'  input');
			setInputVisibility(height, itemDefault['height-visibility']);
			//spacing
			var spacing = $('#customize-control-ht_webfont_spacing_'+index+'  input');
			setInputVisibility(spacing, itemDefault['spacing-visibility']);

		} catch (err){
			console.log("unexpected item error at index->"+index);
		}
		
	}

	//set an input visibility
	function setInputVisibility(input, visibility){
		var inputLabel = input.prev('span');
		switch(visibility){
			case 0:
				//hidden
				input.hide();
				inputLabel.hide();
				break;
			case 1:
				//readonly
				input.prop('disabled', true); 
				break;
			case 2:
				//editable
				//no code required
				break;
			default:
				break;
		}
	}

	//called when any value is updated to make the change to the entire font css using jQuery
	function updateValues(index){
		//name
		var name = $('#customize-control-ht_webfont_name_'+index+' input').val() || '';
		//selector
		var selector = $('#customize-control-ht_webfont_selector_'+index+' input').val() || '';
		//font source
		var source = $('#customize-control-ht_webfont_source_'+index+' select').val() || '';
		//font family
		var family = $('#customize-control-ht_webfont_family_'+index+' select').val() || '';
		//style
		var style = $('#customize-control-ht_webfont_style_'+index+' select').val() || '';
		//color
		var color = $('#customize-control-ht_webfont_color_'+index+'  input').val() || '';
		//size
		var size = $('#customize-control-ht_webfont_size_'+index+'  input').val() || '';
		//height
		var height = $('#customize-control-ht_webfont_height_'+index+'  input').val() || '';
		//spacing
		var spacing = $('#customize-control-ht_webfont_spacing_'+index+'  input').val() || '';


		var target = jQuery('#customize-preview iframe').contents().find(selector);

		if(target.length>0){

			var listFontWeights = ['100', '100italic', '200', '200italic', '300', '300italic', '400', '400italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic'];

			//clear style
			$(target).attr('style', '');
			$(target).css( 'color', color );

			var fontFam = (source=='gfonts') ? '\''+family+'\'' : family;

			//if google fonts append link to head
			if(source=='gfonts'){
				var fontFamilyUrl = family.split(" ").join("+");
				var googleFontPath = "http://fonts.googleapis.com/css?family="+fontFamilyUrl+":"+listFontWeights.join(); +"";
				var googleFontSource = "<link id='ht-webfont-"+index+"-font-family' href='"+googleFontPath+"' rel='stylesheet' type='text/css'>";
				jQuery('#customize-preview iframe').contents().find('head').append(googleFontSource);
			}

			var fontWeight = style.replace(/\D/g,'');
			var fontStyle = style.replace(/[0-9]/g, '');

			var css 	= 'font-family: ' + fontFam + ' sans-serif !important; ' 
					 	+ 'color: ' + color + ' !important; ' 
					 	+ 'font-weight: ' + fontWeight + ' !important; ' 
					 	+ 'font-style: ' + fontStyle + ' !important; '
					 	+ 'font-size: ' + size + 'px !important; ' 
					 	+ 'line-height: ' + height + 'px !important; ' 
					 	+ 'letter-spacing: ' + spacing + 'px !important; ' ;
			$(target).attr('style', css);
		
		}					
	}

	//add the Add New Webfont button to the DOM
	$(document).one('click', '#accordion-panel-ht_webfont_pane', function() {
		addNewWebfontButton();
	});

	//adds a Add New Font  input and button to the DOM
	function addNewWebfontButton() {
		var newBtn = '';
		newBtn += '<li id="customize-control-add-new-webfont" class="panel-meta accordion-section control-section cannot-expand">';
		newBtn +=	'<input type="text" id="new-font-name" class="new-font-name-input" value="" placeholder="' + i18n.newNamePlaceholder + '"/>'
		newBtn +=	'<input type="button" id="ht-new-font-add-btn" class="new-font-name-btn button" value="' + i18n.newNameAddBtn + '"/>'
		newBtn += '</li>';
		$(newBtn).insertAfter($('#accordion-panel-ht_webfont_pane ul.accordion-sub-container li.accordion-section').last());
		//ajaxify new button
		ajaxifyNewWebfontButton();
	}

	//ajaxify the add new font button
	function ajaxifyNewWebfontButton(){
		
			$('#ht-new-font-add-btn').live('click', function(){
				if(isSaved()){
					addNewFontRequest();
				} else {
					alert(i18n.pleaseSaveNew);
				}
			});		
	}

	//the add new font post request
	function addNewFontRequest(){
		newFontName = getNewFontName();

		//check the new font name is defined
		if(newFontName==undefined || newFontName==""){
			alert(i18n.nameRequired);
			return;
		}
			
		$.post( url = ajaxurl + "?addnewcustomfont",
            data = {
                'action': 'ht_add_custom_font',
                'security': htWebfonts.ajaxnonce,
                'name': newFontName
            },
            success = function(data, textStatus, jqXHR){
            	if(typeof data === 'undefined'){
            		//error, probably security failure

                } else {
                    if(data.state == 'success'){
                       	var index = data.index;
						//if success, reload
						window.location = "?openaccordian="+index;
                    } else {
                    	console.log(data.message);
                    }
                }

            },
            dataType = 'json'
        );
	}

	//returns the new font name
	function getNewFontName(){
		var newFontName = '';
		newFontName = $('#new-font-name').val();
		return newFontName;
	}


	//call the ajaxify delete web font buttons on DOM load
	ajaxifyDeleteWebfontButton(); 

	//ajaxifies the delete webfont button
	function ajaxifyDeleteWebfontButton(){
		
			$('.delete-ht-custom-font').live('click', function(){
				var callingButton = $(this);
				var deleteID = callingButton.attr('data-index');
				if(isSaved()){
					var deleteContinue=confirm(i18n.confirmDelete);
					if(deleteContinue)
						deleteFontRequest(deleteID);
				} else {
					alert(i18n.pleaseSaveDelete);
				}
			});		
		
	}

	//call the ajaxify restore web font buttons on DOM load
	ajaxifyRestoreWebfontButton(); 

	//ajaxifies the restore webfont button
	function ajaxifyRestoreWebfontButton(){
		
			$('.restore-ht-theme-font').live('click', function(){
				var callingButton = $(this);
				var restoreID = callingButton.attr('data-index');
				//todo - i18n this string
				var restoreContinue=confirm(i18n.confirmRestore);
				if(restoreContinue)
					restoreFont(restoreID);
			});		
		
	}

	//the delete font post request
	function deleteFontRequest(deleteID){
		var index = deleteID;

		$.post( url = ajaxurl + "?deletecustomfont",
            data = {
                'action': 'ht_delete_custom_font',
                'security': htWebfonts.ajaxnonce,
                'index': index
            },
            success = function(data, textStatus, jqXHR){
            	if(typeof data === 'undefined'){
            		//error, probably security failure

                } else {
                    if(data.state == 'success'){                    
                       	var index = data.index;
						//if success, reload
						window.location = "?openaccordian="+index;
                    } else {
                    	console.log(data.message);
                    }
                }

            },
            dataType = 'json'
        );
	}

	//restore font with given id/index
	function restoreFont(id){
		var defaults = htWebfonts.defaults;
		//name
		var name = defaults[id].name;
		$('#customize-control-ht_webfont_name_'+id+' input').val(name);
		wp.customize( 'ht_webfont_name_'+id, function(obj) { obj.set(name); } );
		//selector
		var selector = defaults[id].selector;
		$('#customize-control-ht_webfont_selector_'+id+' input').val(selector);
		wp.customize( 'ht_webfont_selector_'+id, function(obj) { obj.set(selector); } );
		//font source
		var source = defaults[id].source;
		$('#customize-control-ht_webfont_source_'+id+' select').val(source);
		wp.customize( 'ht_webfont_source_'+id, function(obj) { obj.set(source); } );
		//activate drop down hide
		hideOtherFontDropDownsForIndex(id);
		//font family
		var family = defaults[id].family;
		$('#customize-control-ht_webfont_family_'+id+' select').val(family);
		wp.customize( 'ht_webfont_family_'+id, function(obj) { obj.set(family); } );
		//style
		var style = defaults[id].style;
		$('#customize-control-ht_webfont_style_'+id+' select').val(style);
		wp.customize( 'ht_webfont_style_'+id, function(obj) { obj.set(style); } );
		//color
		var color = defaults[id].color;
		$('#customize-control-ht_webfont_color_'+id+'  input').val(color);
		wp.customize( 'ht_webfont_color_'+id, function(obj) { obj.set(color); } );
		//size
		var size = defaults[id].size;
		$('#customize-control-ht_webfont_size_'+id+'  input').val(size);
		wp.customize( 'ht_webfont_size_'+id, function(obj) { obj.set(size); } );
		//height
		var height = defaults[id].height;
		$('#customize-control-ht_webfont_height_'+id+'  input').val(height);
		wp.customize( 'ht_webfont_height_'+id, function(obj) { obj.set(height); } );
		//spacing
		var spacing = defaults[id].spacing;
		$('#customize-control-ht_webfont_spacing_'+id+'  input').val(spacing);
		wp.customize( 'ht_webfont_spacing_'+id, function(obj) { obj.set(spacing); } );

		//update values
		updateValues(id);

		//enable save
		enableSave();

	}

	//the accordion switch function
	function accordionSwitch ( el ) {
		el = $(el);
		sectionContent   = $( '.accordion-section-content' );
		var section = el.closest( '.accordion-section' ),
			siblings = section.closest( '.accordion-container' ).find( '.open' ),
			content = section.find( sectionContent );

		if ( section.hasClass( 'cannot-expand' ) )
			return;

		if ( section.hasClass( 'open' ) ) {
			section.toggleClass( 'open' );
			content.toggle( true ).slideToggle( 150 );
		} else {
			siblings.removeClass( 'open' );
			siblings.find( sectionContent ).show().slideUp( 150 );
			content.toggle( false ).slideToggle( 150 );
			section.toggleClass( 'open' );
		}
	}

	//get a url parameter, helper function
	function getUrlParameter(sParam){
		var sPageURL = window.location.search.substring(1);
	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++) 
	    {
	        var sParameterName = sURLVariables[i].split('=');
	        if (sParameterName[0] == sParam) 
	        {
	            return sParameterName[1];
	        }
	    }
	}

	//initial load open accordion functionality
	var openAccordian = getUrlParameter('openaccordian');

	if(openAccordian!=undefined && openAccordian!=""){
		//open accordian section
		accordionSwitch('#accordion-section-ht_webfont');

		//set the focus after 3 seconds - 
		setTimeout(function(){
			var control = $('#customize-control-ht_webfont_name_' + openAccordian + ' input');
			if(control.length==0)
				return;
			var controlPosition = control.position().top
			$('.accordion-container').scrollTop(controlPosition);
			$('#customize-control-ht_webfont_name_' + openAccordian + ' input').focus();
		},3000);
	}

	//triggers the save and publish
	function enableSave(){
		wp.customize.trigger('change');
	}

	//check the state is 'saved'
	function isSaved(){
		var result = $('#save').is(':disabled');
		//alert(result);
		return result
	}


	var googleFontsCount = htWebfonts.fontCount.gfonts;
	var websafeFontsCount = htWebfonts.fontCount.websafe;

	//indicies
	// 0 -> themedefault
	var defaultIndex = 0;
	// 1 -> websafe fonts
	var websafeFontsIndex = 1;
	// defaultIndex + websafeFontsCount -> google fonts
	var googleFontsIndex = websafeFontsIndex + websafeFontsCount;
	// defaultIndex + websafeFontCount + googleFontsCount -> typekit?

	var totalFonts = googleFontsCount + websafeFontsCount;

	for (var i = 0; i < 400; i++) {
		var item = $('#customize-control-ht_webfont_source_'+i+' select');
		if(item.length=0)
			continue;	

		//assign the variable an index
		$('#customize-control-ht_webfont_source_'+i+' select').attr('index', i);		

		//initial load
		$('#customize-control-ht_webfont_source_'+i+' select').each(function(){ 
				var index = $(this).attr('index');
				var selected = $(this).val();
				if(selected=='gfonts'){
					hideOtherFontDropdownOptions('#customize-control-ht_webfont_family_'+index+' select', googleFontsIndex, googleFontsCount);
				} else if(selected=='websafe'){
					hideOtherFontDropdownOptions('#customize-control-ht_webfont_family_'+index+' select', websafeFontsIndex, websafeFontsCount);
				}
				var selectedFont = $('#customize-control-ht_webfont_family_'+index+' select').val();
				changeAvailableStyles(index, selectedFont);
			});

		//load the onchange function 
		$('#customize-control-ht_webfont_source_'+i+' select').live('change load', function(e){ 
				var index = $(this).attr('index');
				var selected = $(this).val();
				if(selected=='gfonts'){
					hideOtherFontDropdownOptions('#customize-control-ht_webfont_family_'+index+' select', googleFontsIndex, googleFontsCount);
				} else if(selected=='websafe'){
					hideOtherFontDropdownOptions('#customize-control-ht_webfont_family_'+index+' select', websafeFontsIndex, websafeFontsCount);
				}
				var selectedFont = $('#customize-control-ht_webfont_family_'+index+' select').val();
				changeAvailableStyles(index, selectedFont);
			});


		//initial load
		$('#customize-control-ht_webfont_family_'+i+' select').attr('index', i);

		//load the onchange function 
		$('#customize-control-ht_webfont_family_'+i+' select').each(function(){ 
				var index = $(this).attr('index');
				var selected = $(this).val();
				changeAvailableStyles(index, selected);
			})

		//load the onchange function 
		$('#customize-control-ht_webfont_family_'+i+' select').live('change load', function(e){ 
				var index = $(this).attr('index');
				var selected = $(this).val();
				changeAvailableStyles(index, selected);
			});
	};

	//hides other font selection drop down options for a given custom font index
	function hideOtherFontDropDownsForIndex(index){
		var selected = $('#customize-control-ht_webfont_source_'+index+' select').val();
		if(selected=='gfonts'){
			hideOtherFontDropdownOptions('#customize-control-ht_webfont_family_'+index+' select', googleFontsIndex, googleFontsCount);
		} else if(selected=='websafe'){
			hideOtherFontDropdownOptions('#customize-control-ht_webfont_family_'+index+' select', websafeFontsIndex, websafeFontsCount);
		}

		var selectedFont = $('#customize-control-ht_webfont_family_'+index+' select').val();
		changeAvailableStyles(index, selectedFont);

	}

	//hides other drop down options for a select item give a selector, start index and count
	function hideOtherFontDropdownOptions(selector, startIndex, count){
		var currentVal = $(selector).val();
		//select all
		var options = $(selector + ' option');
		//first show all 
		options.show();
		//do we need to reset the selected value
		var resetSelected = false;


		//iterate through options, hiding those not relevant
		for (var i = 1; i < options.length; i++) {
			if(i<startIndex||i>=startIndex+count){
				var currentOption = $(options[i]);
				var optionVal = currentOption.val();
				if(optionVal==currentVal){
					resetSelected = true;
				}
				$(options[i]).hide();
			}
			
		};
		//select first item in list  if needed
		if(resetSelected==true){
			$(selector).val($(options[startIndex]).val());
		}

	}


	function changeAvailableStyles(id, font){
		hideOtherStylesInDropDown('#customize-control-ht_webfont_style_'+id+' select', variants[font], id);
	}


	function hideOtherStylesInDropDown(selector, itemsToShowArray, id){
		var currentVal = $(selector).val();
		//select all
		var options = $(selector + ' option');
		//first show all 
		options.show();
		//do we need to reset the selected value
		var resetSelected = false;

		var firstUnhiddenIndex = -1;

		//iterate through options, hiding those not relevant
		
		for (var i = 0; i < options.length; i++) {
				var currentOption = $(options[i]);
				var optionVal = currentOption.val();
				//if current value is  in items to show, continue, else hide it
				if($.inArray(optionVal, itemsToShowArray)>-1){
					if(firstUnhiddenIndex<0){
						//set first unhidden index
						firstUnhiddenIndex = i;
					}
					continue;
				} else {
					if(optionVal==currentVal){
						resetSelected = true;
					}
					$(options[i]).hide();	
				}
		};
	

		//select first item in list  if needed
		if(resetSelected==true){
			console.log('reset');
			$(selector).val($(options[firstUnhiddenIndex]).val());
		}

		//set new style in customize object
		var style = $(selector).val();
		wp.customize( 'ht_webfont_style_'+id, function(obj) { obj.set(style); } );

	}



});