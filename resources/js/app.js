import {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Paragraph,
    Heading,
    Alignment,
    List,
    Table,
    TableToolbar,
    TableProperties,
    TableCellProperties,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    Undo
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

// Make them available globally for the Blade views
window.ClassicEditor = ClassicEditor;
window.CKEditorPlugins = [
    Essentials,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Paragraph,
    Heading,
    Alignment,
    List,
    Table,
    TableToolbar,
    TableProperties,
    TableCellProperties,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    Undo
];

// Dispatch custom event to signal CKEditor is ready
window.dispatchEvent(new CustomEvent('ckeditor-loaded'));
