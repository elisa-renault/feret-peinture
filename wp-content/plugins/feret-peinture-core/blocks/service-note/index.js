( function ( blocks, blockEditor, element ) {
    const { registerBlockType } = blocks;
    const { RichText, useBlockProps } = blockEditor;
    const { createElement: el } = element;
    registerBlockType( 'feret/service-note', {
        edit: ( { attributes, setAttributes } ) => el( 'aside', useBlockProps( { className: 'fp-service-note' } ),
            el( RichText, {
                tagName: 'h3', value: attributes.title || '', placeholder: 'Titre de la note pratique',
                onChange: ( title ) => setAttributes( { title } ),
            } ),
            el( RichText, {
                tagName: 'p', value: attributes.body || '', placeholder: 'Ajoutez un conseil utile, ou laissez ce bloc vide.',
                onChange: ( body ) => setAttributes( { body } ),
            } )
        ),
        save: () => null,
    } );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element );
