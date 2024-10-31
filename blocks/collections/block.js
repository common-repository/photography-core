(function( blocks, element, editor, components, i18n ) {

	const { registerBlockType } = blocks;
	const { createElement } = element;
	const { InspectorControls } = editor;
	const { SelectControl, ToggleControl } = components;
	const { __ } = i18n;

	registerBlockType( 'photography-core/collections', {
		title: __( 'Collections (MeowApps)', 'photography-core' ),
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
			const { folderId, folderName } = attributes;
			let options = mwt_block_params.folders.map(x => {
				return { value: x.id, label: x.padded_name }
			});
			options.unshift({ value: 0, label: "Main folder" } );
			let info = !attributes.folderName ?
				__('Display the collections', 'photography-core') :
				'Display the collections in "' + folderName + '"';
			let element = null;
			element = createElement(
				'div', { key: 'mwt-collections-div', className: 'mwt-collections' },
				createElement('div', { className: 'mwt-info' },
				createElement('img', { src: mwt_block_params.logo, className: 'mwt-logo' }), info)
			);

			return [
				!!isSelected && createElement(
					InspectorControls,
					{ key: 'inspector' },
					createElement(
						SelectControl,
						{
							label: __( 'Show folders from', 'photography-core' ),
							value: attributes.folderId ? parseInt(attributes.folderId) : 0,
							onChange: function (value) {
								setAttributes({ folderId: value });
								let newName = value > 0 ? mwt_block_params.folders.filter(x => x.id == value)[0]['name'] : '';
								setAttributes({ folderName: newName });
							},
							options: options,
						}
					)
				),
				element
			];
		},

		save: function( { attributes } ) {
			const folderId = attributes.folderId;
			const pFolderId = " folder_id=" + (folderId > 0 ? folderId : '') + "";
			const container = createElement(
				'div', { className: 'mwt-collections' }, "[mwt-collections" + pFolderId + "]"
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
