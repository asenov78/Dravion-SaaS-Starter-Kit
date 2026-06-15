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

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;
window.createPopper = createPopper;

// TipTap rich-text editor Alpine component
document.addEventListener('alpine:init', () => {
    Alpine.data('tiptap', (options = {}) => ({
        editor: null,
        content: options.content ?? '',
        showPreview: false,
        _textarea: null,

        async init() {
            // Capture $refs/$el BEFORE async — unavailable inside promise callbacks
            const el = this.$refs.editorEl;
            const textarea = this.$el.querySelector('textarea[data-tiptap-target]');

            const [
                { Editor },
                { default: StarterKit },
                { default: Link },
                { default: Placeholder },
                { default: Typography },
            ] = await Promise.all([
                import('@tiptap/core'),
                import('@tiptap/starter-kit'),
                import('@tiptap/extension-link'),
                import('@tiptap/extension-placeholder'),
                import('@tiptap/extension-typography'),
            ]);

            this.editor = new Editor({
                element: el,
                extensions: [
                    StarterKit,
                    Link.configure({ openOnClick: false }),
                    Placeholder.configure({ placeholder: options.placeholder ?? '' }),
                    Typography,
                ],
                content: this.content,
                onUpdate: ({ editor }) => {
                    this.content = editor.getHTML();
                    if (this._textarea) this._textarea.value = this.content;
                },
            });
            this._textarea = textarea;
        },

        destroy() {
            this.editor?.destroy();
        },

        execCmd(cmd, value) {
            if (!this.editor) return;
            const chain = this.editor.chain().focus();
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
            return this.editor ? this.editor.isActive(type, attrs) : false;
        },
    }));
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