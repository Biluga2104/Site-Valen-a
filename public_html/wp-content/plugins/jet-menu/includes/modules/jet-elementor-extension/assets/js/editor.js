(function( $ ) {

	'use strict';

	var JetElemExtEditor = {

		init: function() {

			var QueryControlItemView = elementor.modules.controls.Select2.extend({

				hasTitles: false,
				loadEditUrl: false,

				ui: function() {
					
					var ui = elementor.modules.controls.Select2.prototype.ui.apply( this, arguments );

					_.extend( ui, {
						jetEngineCreateButton: 'a.jet-engine-create-listing',
					} );

					return ui;
				},

				events: function() {

					var events = elementor.modules.controls.Select2.prototype.events.apply( this, arguments );

					_.extend( events, {
						'click @ui.jetEngineCreateButton': 'onCreateButtonClick',
					} );

					return events;
	
				},

				getQueryArgs: function() {
					var args = this.model.get( 'query' );

					if ( this.model.get( 'prevent_looping' ) ) {

						if ( !args.post__not_in ) {
							args.post__not_in = [];
						}

						var currentDocID = elementor.documents.getCurrentId();

						if ( -1 === args.post__not_in.indexOf( currentDocID ) ) {
							args.post__not_in.push( currentDocID );
						}

						var $currentDoc = elementor.$previewContents.find('[data-elementor-id="' + elementor.documents.getCurrentId()  + '"]').first(),
							$parentsDocs = $currentDoc.parents( '.elementor[data-elementor-type]' );

						if ( $parentsDocs[0] ) {

							$parentsDocs.each( function() {
								var docID = $( this ).data( 'elementor-id' );

								if ( -1 === args.post__not_in.indexOf( docID ) ) {
									args.post__not_in.push( docID );
								}
							} );
						}
					}

					return args;
				},

				getSelect2DefaultOptions: function getSelect2DefaultOptions() {
					var self = this;

					return jQuery.extend( elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply( this, arguments ), {
						ajax: {
							url: ajaxurl,
							cache: true,
							dataType: 'json',
							data: function( params ) {
								return {
									q:          params.term,
									action:     'jet_query_control_options',
									query_type: self.model.get( 'query_type' ),
									query:      self.getQueryArgs(),
									signature:  self.model.get( 'signature' ),
								};
							},
							processResults: function( response ) {
								return {
									results: response.data.results
								};
							}
						},
						minimumInputLength: 1
					});
				},

				getOptionsTitles: function getOptionsTitles() {
					var self  = this,
						query_ids = this.getControlValue();

					if ( !query_ids ) {
						return;
					}

					if ( !_.isArray( query_ids ) ) {
						query_ids = [query_ids];
					}

					if ( ! query_ids[0] ) {
						return;
					}

					jQuery.ajax( {
						url: ajaxurl,
						dataType: 'json',
						data: {
							action:     'jet_query_control_options',
							query_type: self.model.get( 'query_type' ),
							query:      self.getQueryArgs(),
							ids:        query_ids,
							signature:  self.model.get( 'signature' ),
						},
						beforeSend: function() {
							self.ui.select.prop( 'disabled', true );
						},
						success: function( response ) {
							self.hasTitles = true;

							self.model.set( 'options', self.prepareOptions( response.data.results ) );
							self.render();
						}
					} );
				},

				prepareOptions: function prepareOptions( options ) {
					var result = {};

					jQuery.each( options, function( index, item ) {
						result[ item.id ] = item.text;
					} );

					return result;
				},

				renderEditButton: function renderEditButton() {

					if ( this.loadEditUrl ) {
						return;
					}

					if ( this.model.get( 'multiple' ) ) {
						return;
					}

					var editBtnConfig = this.model.get( 'edit_button' );

					if ( !editBtnConfig || !editBtnConfig.active ) {
						return;
					}

					var self = this,
						value = this.getControlValue(),
						$editBtnWrap = this.$el.find( '.jet-query-edit-btn-wrap' ),
						$editBtn = this.$el.find( '.jet-query-edit-btn' );

					if ( !value ) {
						$editBtnWrap.remove();
						return;
					}

					this.loadEditUrl = true;

					jQuery.ajax( {
						url: ajaxurl,
						dataType: 'json',
						data: {
							action:     'jet_query_get_edit_url',
							id:         value,
							query_type: self.model.get( 'query_type' ),
						},
						success: function( response ) {

							if ( ! response.success ) {
								return;
							}

							if ( ! response.data.edit_url ) {
								$editBtnWrap.remove();
								return;
							}

							var editUrl = response.data.edit_url;

							if ( $editBtn[0] ) {
								$editBtn.attr( 'href', editUrl )
							} else {
								$editBtn = jQuery( '<a>', {
									class: 'elementor-button elementor-button-default jet-query-edit-btn',
									href: editUrl,
									target: '_blank',
									html: '<i class="eicon-pencil"></i>' + editBtnConfig.label,
								} );

								$editBtnWrap = jQuery( '<div>', {
									class: 'jet-query-edit-btn-wrap',
									html: $editBtn,
								} );

								self.$el.find( '.elementor-control-field' ).after( $editBtnWrap );
							}

							self.loadEditUrl = false;
						},
						fail: function() {
							self.loadEditUrl = false;
						}
					} );
				},

				renderCreateButton: function() {

					var createButton = this.model.get( 'create_button' );

					if ( ! createButton ) {
						return;
					}
					
					var $createHandler = jQuery( '<br><span style="display: flex; justify-content: flex-end;"><a href="#" class="jet-engine-create-listing">Create new listing item</a><span>' );
					this.$el.find( '.elementor-control-field' ).after( $createHandler );

					$createHandler

				},

				onCreateButtonClick: function( event ) {
					
					event.preventDefault();
					var createButton = this.model.get( 'create_button' );
					var handler = createButton.handler || 'JetListings';

					if ( window[ handler ] && window[ handler ].onEditorCreateClick ) {
						window[ handler ].onEditorCreateClick( this );
					}
					
				},

				onInputChange: function() {
					this.renderEditButton();
				},

				onReady: function onReady() {

					this.ui.select.select2( this.getSelect2Options() );

					if ( !this.hasTitles ) {
						this.getOptionsTitles();
					}

					this.renderEditButton();
					this.renderCreateButton();
				}
			});

			var RepeaterControlItemView = elementor.modules.controls.Repeater.extend({
				className: function className() {
					return elementor.modules.controls.Repeater.prototype.className.apply( this, arguments ) + ' elementor-control-type-repeater';
				},
				callParentFunction( functionName, args = [] ) {
					const widgetType     = this.options.container.model.get( 'widgetType' );
					const parentFunction = elementor.modules.controls.Repeater.prototype[ functionName ];

					if (  ! elementor.widgetsCache[ widgetType ] || ! elementor.widgetsCache[ widgetType ]?.support_nesting ) {
						parentFunction.apply( this, args );
						return;
					}

					elementor.widgetsCache[ widgetType ].support_nesting = false;
					parentFunction.apply( this, args );
					elementor.widgetsCache[ widgetType ].support_nesting = true;
				},
				onButtonAddRowClick() {
					this.callParentFunction( 'onButtonAddRowClick' );
				},
				onChildviewClickRemove( childView ) {
					this.callParentFunction( 'onChildviewClickRemove', [ childView ] );
				},
				onChildviewClickDuplicate( childView ) {
					this.callParentFunction( 'onChildviewClickDuplicate', [ childView ] );
				}
			});

			var Select2ControlItemView = elementor.modules.controls.Select2.extend({
				className: function className() {
					return elementor.modules.controls.Repeater.prototype.className.apply( this, arguments ) + ' elementor-control-type-select2';
				}
			});

			// Add controls views
			elementor.addControlView( 'jet-query',    QueryControlItemView );
			elementor.addControlView( 'jet-repeater', RepeaterControlItemView );
			elementor.addControlView( 'jet-select2',  Select2ControlItemView );
		}

	};

	$( window ).on( 'elementor:init', JetElemExtEditor.init );

}( jQuery ));