<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Content Types Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-select
			label="<?php _e( 'From Content Type', 'jet-engine' ); ?>"
			description="<?php _e( 'Select content type to get data from', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="contentTypes"
			size="fullwidth"
			v-model="query.content_type"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Number', 'jet-engine' ); ?>"
			description="<?php _e( 'Number of items to show in the listing grid or per page if JetSmartFilters pagination is used. Query Count dynamic tag will show total number of items matched current Query', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			name="query_number"
			v-model="query.number"
		></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Offset', 'jet-engine' ); ?>"
			description="<?php _e( 'Number of items to skip', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			name="query_offset"
			v-model="query.offset"
		></cx-vui-input>
		<cx-vui-select
			label="<?php _e( 'Status', 'jet-engine' ); ?>"
			description="<?php _e( 'Select items only with the certain status', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: '',
					label: 'Any',
				},
				{
					value: 'publish',
					label: 'Publish',
				},
				{
					value: 'draft',
					label: 'Draft',
				},
			]"
			size="fullwidth"
			v-model="query.status"
		></cx-vui-select>
		<cx-vui-input
			label="<?php _e( 'Search Query', 'jet-engine' ); ?>"
			description="<?php _e( 'Search string to search across all CCT fields. IF you need to search by a specific field - please use Query settings and Like compare operator.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			name="search_query"
			v-model="query.search_query"
		><jet-query-dynamic-args v-model="dynamicQuery.search_query"></jet-query-dynamic-args></cx-vui-input>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
			v-if="hasFields()"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Order & Order By', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new sorting parameter', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.order"
					@add-new-item="addNewField( $event, [], query.order )"
				>
					<cx-vui-repeater-item
						v-for="( order, index ) in query.order"
						:title="query.order[ index ].orderby"
						:subtitle="query.order[ index ].order"
						:collapsed="isCollapsed( order )"
						:index="index"
						@clone-item="cloneField( $event, order._id, query.order )"
						@delete-item="deleteField( $event, order._id, query.order )"
						:key="order._id"
					>
						<cx-vui-select
							label="<?php _e( 'Order By', 'jet-engine' ); ?>"
							description="<?php _e( 'Sort retrieved posts by selected parameter', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="orderByOptions"
							size="fullwidth"
							:value="query.order[ index ].orderby"
							@input="setFieldProp( order._id, 'orderby', $event, query.order )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Order', 'jet-engine' ); ?>"
							description="<?php _e( 'Designates the ascending or descending order of the `Order By` parameter', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="[
								{
									value: 'ASC',
									label: 'From lowest to highest values (1, 2, 3; a, b, c)',
								},
								{
									value: 'DESC',
									label: 'From highest to lowest values (3, 2, 1; c, b, a)',
								},
							]"
							size="fullwidth"
							:value="query.order[ index ].order"
							@input="setFieldProp( order._id, 'order', $event, query.order )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Type', 'jet-engine' ); ?>"
							description="<?php _e( 'Data type stored in the given field', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="dataTypes"
							size="fullwidth"
							:value="query.order[ index ].type"
							@input="setFieldProp( order._id, 'type', $event, query.order )"
						></cx-vui-select>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
			v-if="hasFields()"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Query', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.args"
					@add-new-item="addNewField( $event, [], query.args, newDynamicArgs )"
					:custom-actions="[
						{
							buttonLabel: '<?php _e( 'Add new group', 'jet-engine' ); ?>',
							buttonStyle: 'accent-border',
							callback: addNewArgsGroup,
						}
					]"
				>
					<cx-vui-repeater-item
						v-for="( clause, index ) in query.args"
						:collapsed="isCollapsed( clause )"
						:index="index"
						@clone-item="cloneField( $event, clause._id, query.args, newDynamicArgs )"
						@delete-item="deleteField( $event, clause._id, query.args, deleteDynamicArgs )"
						:key="clause._id"
					>
						<jet-engine-cct-query-field
							:field="clause"
							:meta-query="query.args"
							:dynamic-query="dynamicQuery.args[ clause._id ]"
							:current-fields="currentFields"
							@input="setFieldData( clause._id, $event, query.args )"
							@dynamic-input="setDynamicArgs( clause._id, $event )"
						></jet-engine-cct-query-field>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-select
			v-if="1 < query.args.length && ! query.status"
			label="<?php _e( 'Relation', 'jet-engine' ); ?>"
			description="<?php _e( 'The logical relationship between args clauses', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: 'AND',
					label: '<?php _e( 'And', 'jet-engine' ); ?>',
				},
				{
					value: 'OR',
					label: '<?php _e( 'Or', 'jet-engine' ); ?>',
				},
			]"
			size="fullwidth"
			v-model="query.relation"
		></cx-vui-select>
		<cx-vui-component-wrapper
			v-if="1 < query.args.length && query.status"
			label="<?php _e( 'Please note', 'jet-engine' ); ?>"
			description="<?php _e( 'You set <b>Status</b> option, so relation will be forced to <b>And</b> to avoid conflicts between status clause and other clauses.', 'jet-engine' ); ?>"
		></cx-vui-component-wrapper>
		<?php do_action( 'jet-engine/custom-content-types/query-builder-controls' ); ?>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'equalwidth' ]"
			v-if="hasFields()"
			label="<?php _e( 'Fields for filters', 'jet-engine' ); ?>"
			description="<?php _e( 'Available fields list to filter with <b>JetSmartFilters</b> plugin. To filter query results by selected field, copy field name and paste it into <b>Query Variable</b> option of selected filter.', 'jet-engine' ); ?>"
		>
			<div>
				<code v-for="field in currentFields" :style="{ display: 'inline-block', marginBottom: '2px' }">{{ field.value }}</code>
			</div>
		</cx-vui-component-wrapper>
	</div>
</div>
