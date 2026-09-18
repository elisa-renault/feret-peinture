( function ( blocks, blockEditor, element ) {
    const { registerBlockType } = blocks;
    const { RichText, useBlockProps } = blockEditor;
    const { createElement: el } = element;
    registerBlockType( 'feret/service-content', {
        edit: ( { attributes, setAttributes } ) => el( RichText, {
            ...useBlockProps( { className: 'fp-service-content' } ),
            tagName: 'div',
            multiline: 'p',
            value: attributes.content || '',
            placeholder: 'Décrivez les travaux réalisés et les finitions proposées.',
            onChange: ( content ) => setAttributes( { content } ),
        } ),
        save: ( { attributes } ) => el( RichText.Content, {
            ...useBlockProps.save( { className: 'fp-service-content' } ),
            tagName: 'div',
            value: attributes.content || '',
        } ),
    } );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element );
