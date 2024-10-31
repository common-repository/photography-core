(function( blocks, element, editor, components, i18n ) {

	const { registerBlockType } = blocks;
	const { createElement } = element;
	const { InspectorControls } = editor;
	const { SelectControl, ToggleControl } = components;
	const { __ } = i18n;

	registerBlockType( 'photography-core/keywords', {
		title: __( 'Keywords (MeowApps)', 'photography-core' ),
		icon: 'camera',
		category: 'layout', // (common, formatting, layout, widgets, embed)
		keywords: [ __( 'section' ), __( 'header' ) ],
		customClassName: false,
		className: false,
		attributes: {
			folderId: {
				type: 'number',
				default: 0
			},
			folderName: {
				type: 'string',
				default: ""
			},
		},
		supports: {
			align: [ 'full', 'wide' ],
		},

		edit: function( { attributes, setAttributes, isSelected } ) {
			let element = null;
			element = createElement(
				'div', { key: 'mwt-keywords-div', className: 'mwt-keywords' },
				createElement('div', { className: 'mwt-info' },
				createElement('img', { src: mwt_block_params.logo, className: 'mwt-logo' }), 'Display the keywords')
			);
			return element;
		},

		save: function( props ) {
			const container = createElement(
				'div', { className: 'mwt-keywords' }, "[mwt-keywords]"
			);
			return container;
		}

	});

})(
	window.wp.blocks,
	window.wp.element,
	window.wp.editor,
	window.wp.components,
	window.wp.i18n
);
