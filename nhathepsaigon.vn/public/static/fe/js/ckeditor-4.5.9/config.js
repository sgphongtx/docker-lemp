/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	//config.language = 'vi';
    //config.width = '950px';
    config.height = '500px';
    // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
    /*config.toolbar = [
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Scayt' ] },
        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
        { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
        { name: 'tools', items: [ 'Maximize' ] },
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
        { name: 'others', items: [ '-' ] },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
        { name: 'styles', items: [ 'Styles', 'Format' ] }
    ];

    // Toolbar groups configuration.
    config.toolbarGroups = [
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'forms' },
        { name: 'tools' },
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'others' },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'styles' },
        { name: 'colors' }
    ];
    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Subscript,Superscript';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'image:advanced;link:advanced';*/

    //config.enterMode = CKEDITOR.ENTER_BR;
    config.filebrowserBrowseUrl = STATIC_URL + "/fe/js/ckeditor-4.5.9/kcfinder-3.12/browse.php?type=files";
    config.filebrowserImageBrowseUrl = STATIC_URL + "/fe/js/ckeditor-4.5.9/kcfinder-3.12/browse.php?type=images";
    config.filebrowserFlashBrowseUrl = STATIC_URL + "/fe/js/ckeditor-4.5.9/kcfinder-3.12/browse.php?type=flash";
    config.filebrowserUploadUrl = STATIC_URL + "/fe/js/ckeditor-4.5.9/kcfinder-3.12/upload.php?type=files";
    config.filebrowserImageUploadUrl = STATIC_URL + "/fe/js/ckeditor-4.5.9/kcfinder-3.12/upload.php?type=images";
    config.filebrowserFlashUploadUrl = STATIC_URL + "/fe/js/ckeditor-4.5.9/kcfinder-3.12/upload.php?type=flash";
};
