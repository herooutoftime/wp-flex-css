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
				"click .add-row": "addRow"
			},
			addRow: function() {
				this.template();
				event.preventDefault();
			},
			template: function() {
				var row_index = $('table.flex-css-table tbody tr').length;
				var new_row = $("table.flex-css-table tbody tr:first").clone();
				new_row.find("td").each(function() {
					if(!$(this).hasClass('column-value'))
						return;

					$(this).empty().append($('<input />', {
						'class': 'input-text',
						'type': 'text',
						'data-colname': $(this).attr('data-colname').toLowerCase()
					}));
				}).find("input, select").each(function() {
					$(this).attr({
						'name': function(_, name) { return 'flex_css_data[' + row_index + '][' + $(this).attr('data-colname') + ']' },
						'value': function(_, name) { return $(this).attr('data-colname') == 'id' ? row_index : ''; }
					});
				});
				$('table.flex-css-table tbody').append(new_row);
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

		var codemirror = CodeMirror.fromTextArea(document.getElementById('preview'), {
			height: '200px'
			,mode: 'css'
			,readOnly: true
			,lineNumbers: true
			,renderLine: function(cm, lh, el) {
				console.log(lh);
			}
			,foldGutter: true
			,gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
			//,lineNumberFormatter: function(line) {
			//	return '#' + line;
			//}
		});

		var goToLine = function(line) {
			var t = codemirror.charCoords({line: line, ch: 0}, "local").top;
			codemirror.scrollTo(null, t - 25);
		};

		$('body').on('click', '.gotoline', function(){
			goToLine($(this).attr('data-linenumber'));
		});

		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'flex_css_get_file_contents'
			}
		}).done(function(data){
			var data = JSON.parse(data);
			console.log(data);
			var makeLinkPanel = function(editable) {
				var node = document.createElement("div");
				node.className = 'panel top';
				node.style = 'padding:5px;background:#f7f7f7;border-bottom: 1px solid #ddd;font-size:11px';
				var icon = node.appendChild(document.createElement("span"));
				icon.className = 'dashicons dashicons-yes';
				var makeLineLinks = function(editable) {
					//span.textContent = 'Editable line numbers are: ';

					for(var key in editable) {
						var variable = node.appendChild(document.createElement("span"));
						variable.innerHTML = key + ': ';
						for(var i = 0, len = editable[key].length; i < len; i++) {
							var link = node.appendChild(document.createElement("a"));
							link.setAttribute('href', '#' + editable[key][i]);
							link.className = 'gotoline';
							link.setAttribute('data-linenumber', editable[key][i]);
							link.textContent = editable[key][i];
							node.innerHTML += "&nbsp;&nbsp;";
						}
					}

					//for (var i = 0, len = editable.length; i < len; i++) {
					//	var link = span.appendChild(document.createElement("a"));
					//	link.setAttribute('href', '#' + editable[i]);
					//	link.className = 'gotoline';
					//	link.setAttribute('data-linenumber', editable[i]);
					//	link.textContent = editable[i];
					//	span.innerHTML += "&nbsp;";
					//}
					return;
				};
				makeLineLinks(editable);
				return node;
			};
			var makeReadOnlyPanel = function() {
				var node = document.createElement("div"), text = [];
				node.className = 'panel bottom';
				node.style = 'padding:5px;background:#f7f7f7;border-bottom: 1px solid #ddd;';
				var icon = node.appendChild(document.createElement("span"));
				icon.className = 'dashicons dashicons-warning';

				var span = node.appendChild(document.createElement("span"));
				if(data.minified)
					text.push('Minified CSS can\'t be edited');
				if(!data.writable)
					text.push('CSS is not writable');
				span.textContent = text.join(', ');
				return node;
			};
			codemirror.setOption('readOnly', data.minified || !data.writable);
			if(data.minified || !data.writable) {
				codemirror.addPanel(makeReadOnlyPanel(), {
					position: 'top'
				});
			} else {
				codemirror.addPanel(makeLinkPanel(data.editable), {
					position: 'top'
				});
			}

			//codemirror.setOption('renderLine', function(cm, lh, el) {
			//	console.log(lh);
			//});
			codemirror.setValue(data.content);
			//if(!data.minified)
			//	goToLine(data.editable[0]);
			//codemirror.setValue('lineNumberFormatter')
		});
	});
})( jQuery );
