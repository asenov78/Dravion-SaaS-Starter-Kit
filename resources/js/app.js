import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
// FullCalendar
import { Calendar } from '@fullcalendar/core';
// Popper (for table dropdowns)
import { createPopper } from '@popperjs/core';

// HTML beautifier for source view
import { html as beautifyHtml } from 'js-beautify';

// TipTap — static imports (no async complexity)
import { Editor } from '@tiptap/core';
import { StarterKit } from '@tiptap/starter-kit';
import { Link } from '@tiptap/extension-link';
import { Placeholder } from '@tiptap/extension-placeholder';
import { Typography } from '@tiptap/extension-typography';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;
window.createPopper = createPopper;

// TipTap rich-text editor Alpine component
// NOTE: editor stored in closure var, NOT as Alpine reactive property.
// Alpine Proxy wraps reactive props → ProseMirror tr.before !== raw state → "mismatched transaction".
document.addEventListener('alpine:init', () => {
    Alpine.data('tiptap', (options = {}) => {
        let _editor = null; // raw, non-proxied reference

        return {
            content: options.content ?? '',
            showPreview: options.preview ?? true,
            showHtml: false,

            init() {
                _editor = new Editor({
                    element: this.$refs.editorEl,
                    extensions: [
                        StarterKit,
                        Link.configure({ openOnClick: false }),
                        Placeholder.configure({ placeholder: options.placeholder ?? '' }),
                        Typography,
                    ],
                    content: this.content,
                    onUpdate: ({ editor }) => {
                        this.content = editor.getHTML();
                    },
                    onSelectionUpdate: ({ editor }) => {
                        if (!this.showPreview) return;
                        const { from } = editor.state.selection;
                        const doc = editor.state.doc;
                        // Find index of top-level block containing the cursor
                        let blockIndex = 0;
                        doc.forEach((node, offset) => {
                            if (from > offset + node.nodeSize) blockIndex++;
                        });
                        const panel = this.$el.querySelector('[data-preview-content]');
                        if (!panel) return;
                        const target = panel.children[blockIndex];
                        if (target) {
                            // Scroll only the preview panel, not the page
                            const panelRect = panel.getBoundingClientRect();
                            const targetRect = target.getBoundingClientRect();
                            const offset = targetRect.top - panelRect.top;
                            panel.scrollTop += offset - 20;
                        }
                    },
                });
                // Always keep data-tiptap-target in sync with content
                this.$watch('content', (val) => {
                    const ta = this.$el.querySelector('textarea[data-tiptap-target]');
                    if (ta) ta.value = val;
                });
            },

            toggleHtml() {
                this.showHtml = !this.showHtml;
                if (this.showHtml) {
                    // Format HTML when opening source view
                    this.content = beautifyHtml(this.content, {
                        indent_size: 2,
                        wrap_line_length: 100,
                        preserve_newlines: false,
                        end_with_newline: false,
                    });
                } else if (_editor) {
                    // Switching back to WYSIWYG — push edited HTML into editor
                    _editor.commands.setContent(this.content, false);
                }
            },

            destroy() {
                _editor?.destroy();
                _editor = null;
            },

            execCmd(cmd) {
                if (!_editor) return;
                const chain = _editor.chain().focus();
                switch (cmd) {
                    case 'bold':        chain.toggleBold().run(); break;
                    case 'italic':      chain.toggleItalic().run(); break;
                    case 'strike':      chain.toggleStrike().run(); break;
                    case 'h2':          chain.toggleHeading({ level: 2 }).run(); break;
                    case 'h3':          chain.toggleHeading({ level: 3 }).run(); break;
                    case 'ul':          chain.toggleBulletList().run(); break;
                    case 'ol':          chain.toggleOrderedList().run(); break;
                    case 'blockquote':  chain.toggleBlockquote().run(); break;
                    case 'code':        chain.toggleCode().run(); break;
                    case 'codeBlock':   chain.toggleCodeBlock().run(); break;
                    case 'hr':          chain.setHorizontalRule().run(); break;
                    case 'undo':        chain.undo().run(); break;
                    case 'redo':        chain.redo().run(); break;
                    case 'link': {
                        const url = prompt('URL:');
                        if (url) chain.setLink({ href: url }).run();
                        break;
                    }
                    case 'unlink':      chain.unsetLink().run(); break;
                }
            },

            isActive(type, attrs) {
                return _editor ? _editor.isActive(type, attrs) : false;
            },
        };
    });
});

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports
    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then(module => module.initChartThirteen());
    }

    // Calendar init
    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }
});