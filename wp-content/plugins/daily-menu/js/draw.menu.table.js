/**
 * Draw the table of dishes
 */

(function($) {

		    //Prepare jTable
			$('#MenusTableContainer').jtable({
				title: objectL10n.table_title,
				pageSize: 20,
				paging: true,
				actions: {
					listAction: ajax_object.ajax_url.concat('?action=list_menus'),
					createAction: ajax_object.ajax_url.concat('?action=create_menu'),
					updateAction: ajax_object.ajax_url.concat('?action=update_menu'),
					deleteAction: ajax_object.ajax_url.concat('?action=delete_menu')
				},
				fields: {
					date: {
						key: true,
						create: true,
						edit: true,
						list: true,
						type: 'date',
						displayFormat: objectL10n.date_format
					},
					id_starter: {
						title: objectL10n.column_starter_title,
						options: ajax_object.ajax_url.concat('?action=list_dishes_from_type&type=STA')
					},
					id_maincourse: {
						title: objectL10n.column_maincourse_title,
						options: ajax_object.ajax_url.concat('?action=list_dishes_from_type&type=MAI')
						//width: '40%'
					},
					id_accompaniment: {
						title: objectL10n.column_accompaniment_title,
						options: ajax_object.ajax_url.concat('?action=list_dishes_from_type&type=ACC')
					},
					id_dairy: {
						title: objectL10n.column_dairy_title,
						options: ajax_object.ajax_url.concat('?action=list_dishes_from_type&type=DAI')
					},
					id_dessert: {
						title: objectL10n.column_dessert_title,
						options: ajax_object.ajax_url.concat('?action=list_dishes_from_type&type=DES')
					}
				}
			});

			//Load person list from server
			$('#MenusTableContainer').jtable('load');

	})(jQuery);