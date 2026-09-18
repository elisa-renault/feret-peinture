( function ( blocks, blockEditor, element ) {
    const { registerBlockType } = blocks;
    const { RichText, useBlockProps } = blockEditor;
    const { createElement: el } = element;
    registerBlockType( 'feret/site-copy', {
        edit: ( { attributes, setAttributes } ) => el( 'section', useBlockProps( { className: 'fp-site-copy' } ),
            el( 'p', { className: 'fp-site-copy__label' }, attributes.label || 'Texte du site' ),
            el( RichText, { tagName: 'p', value: attributes.content || '', placeholder: 'Écrivez le texte à afficher.', onChange: ( content ) => setAttributes( { content } ) } )
        ),
        save: () => null,
    } );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element );
