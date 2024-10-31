(function( blocks, i18n, element, editor ) {

	const { __ } = i18n;
	const { registerBlockType } = blocks;
	const { RichText } = editor;
	const { createElement } = element;
	//const { children } = blocks.source;

	registerBlockType( 'photography-core/section-header', {
		title: __( 'Section Header (MeowApps)', 'photography-core' ),
		icon: 'camera',
		category: 'layout', // (common, formatting, layout, widgets, embed)
		keywords: [ __( 'section' ), __( 'header' ) ],
		customClassName: false,
		className: false,
		attributes: {
			content: {
				type: 'array',
				source: 'children',
				selector: 'h3',
				default: 'My title'
			}
		},
		supports: {
			align: [ 'full', 'wide' ],
		},

		edit: function( props ) {
			const content = props.attributes.content;
			const focus = props.focus;

			function onChangeContent( newContent ) {
				props.setAttributes( { content: newContent } );
			}

			const editableHeader = createElement(
				RichText,
				{
					tagName: 'h3',
					onChange: onChangeContent,
					value: content,
					focus: focus,
					onFocus: props.setFocus,
				}
			);
			return createElement(
				'div', { key: 'mwt-section-header-div', className: 'mwt-section-header' },
				editableHeader,
				createElement('div', { className: 'line' })
			);
		},

		save: function( props ) {
			const content = props.attributes.content;
			const container = createElement(
				'div', { className: 'mwt-section-header' },
				createElement( RichText.Content, { tagName: 'h3', value: content }),
				createElement('div', { className: 'line' }) );
			return container;
		}

	});

})(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element,
	window.wp.editor
);