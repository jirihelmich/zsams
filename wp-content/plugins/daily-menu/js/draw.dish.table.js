/**
 * Draw the table of dishes
 */
(function($) {

		    //Prepare jTable
			$('#DishesTableContainer').jtable({
				title: objectL10n.table_title,
				pageSize: 20,
				paging: true,
				sorting: true,
				multiSorting: true,
				actions: {
					listAction: ajax_object.ajax_url.concat('?action=list_dishes'),
					createAction: ajax_object.ajax_url.concat('?action=create_dish'),
					updateAction: ajax_object.ajax_url.concat('?action=update_dish'),
					deleteAction: ajax_object.ajax_url.concat('?action=delete_dish')
				},
				fields: {
					id_dish: {
						key: true,
						create: false,
						edit: false,
						list: false
					},
					name: {
						title: objectL10n.column_name_title,
						sorting: true
						//width: '40%'
						//type: 'date',
					},
					composition: {
						title: objectL10n.column_composition_title,
						sorting: false
						//width: '40%'
						//type: 'date',
					},
					type: {
						title: objectL10n.column_type_title,
						options: ajax_object.ajax_url.concat('?action=list_types_options')
						//width: '40%'
						//type: 'date',
					},
					sstype: {
						title: objectL10n.column_sstype_title,
						dependsOn: 'type',
						options: function (data) {
	                        return ajax_object.ajax_url.concat('?action=list_sstypes_options&type=' + data.dependedValues.type);
	                    }
						//width: '40%'
						//type: 'date',
					},
					picture: {
						title: objectL10n.column_picture_title,
						sorting: false,
					    input: function (data) {
					    	if (data.value) {
					    		html = '<input type="hidden" name="picture" value="' + data.value + '" />';
					    	} else {
					    		html = '<input type="hidden" name="picture" />';
					    	}
					    	html += '<a href="#" onClick="javascript: selectPicture();">';
					    	html += objectL10n.column_picture_text;
					    	html += '</a>';
					    	return html;
					    },
						//create: false,
						//edit: false,
						list: false						
					}
				}
			});

			//Load person list from server
			$('#DishesTableContainer').jtable('load');

	})(jQuery);

function selectPicture() {

	//event.preventDefault();

	var frame = wp.media({
	    title: "Select Image",
	    multiple: false,
	    library: { type: 'image' },
	    button : { text : 'add image' }
	});
	
	frame.on( 'select', function() {
	    var selection = frame.state().get('selection');
	    selection.each(function(attachment) {
	        // this will return an object with all the attachment-details
	        jQuery('input#Edit-picture')[0].value = attachment.id;
	    });
	});
	
	frame.open();
}

