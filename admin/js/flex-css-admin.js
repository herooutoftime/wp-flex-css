(function( $ ) {
	'use strict';

	$(function() {
		var FlexCssTableView,
			FlexCssTableNavView,
			FlexCssTableRowView,

			FlexCssModel,
			FlexCssColl,

			flexCssTableNavView,
			flexCssTableRowView,
			flexCssTableView;

		$.ajaxSetup({ cache: false });

		FlexCssModel = Backbone.Model.extend({
			id: null,
			property: null,
			value: null,
			toJSON: function() {
				var data;
				var json = Backbone.Model.prototype.toJSON.call(this);
				_.each(json, function(value, key)
				{
					data = key;
				}, this);
				return data;
			}
		});
		FlexCssColl = Backbone.Collection.extend({
			model: FlexCssModel,
			url: ajaxurl,
			parse: function(response) {
				return response;
			}
		});

		FlexCssTableView = Backbone.View.extend({
			events: {
				"focus .autocomplete": 'getAutocomplete',
				"keydown .autocomplete": 'fetchCollection',
				"change .changetype": 'changeType'
			},
			initialize: function () {
				this.coll = new FlexCssColl();
				this.collectionFetched = false;
				console.log('Inside Init');

				this.render = _.wrap(this.render, function(render) {
					this.beforeRender();
					render();
					this.afterRender();
				});

				this.render();
			},
			render: function () {
				console.log('Inside render');
				$('input.input-colorpicker').wpColorPicker();
				return this;
			},
			beforeRender: function () {
				console.log("Before render");
			},
			afterRender: function () {
				console.log("After render");
			},
			fetchCollection: function() {
				//if (this.collectionFetched) return;
				this.coll.fetch({
					data: {
						action: 'flex_css_getvars'
					}
				});
				this.collectionFetched = true;
			},
			getAutocomplete: function () {
				$(".autocomplete").autocomplete({
					source: ajaxurl + '?action=flex_css_getvars',
					minLength : 2
				});
			},
			changeType: function() {
				console.log('Change Type');
				console.log($(event.target).closest('tr').find('td.column-value input'));
				$(event.target).closest('tr').find('td.column-value input').wpColorPicker();
			}
		});

		FlexCssTableNavView = Backbone.View.extend({
			events: {
				"click .add-row": "addRow",
			},
			addRow: function() {
				this.template();
				event.preventDefault();
			},
			template: function() {
				var row_index = $('table.flex-css-table tbody tr').length;
				$("table.flex-css-table tbody tr:first").clone().find("input").each(function() {
					$(this).attr({
						'name': function(_, name) { return 'flex_css_data[' + row_index + '][' + $(this).attr('data-colname') + ']' },
						'value': function(_, name) { return $(this).attr('data-colname') == 'id' ? row_index : ''; }
					});
				}).end().appendTo("table.flex-css-table");
			}
		});

		FlexCssTableRowView = Backbone.View.extend({
			events: {
				"click .delete-row": "deleteRow"
			},
			deleteRow: function() {
				console.log('deleteRow!');
				console.log(this);
				console.log(event);
				$(event.target).closest('tr').remove();
				event.preventDefault();
			}
		});

		flexCssTableNavView = new FlexCssTableNavView({ el: $('.tablenav') });
		flexCssTableRowView = new FlexCssTableRowView({ el: $('table.flex-css-table') });
		flexCssTableView = new FlexCssTableView({ el: $('table.flex-css-table') });
	});
})( jQuery );
