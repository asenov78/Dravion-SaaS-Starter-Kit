<div class="inline-flex items-center rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden text-xs font-medium">
    <button type="button"
        @click="tab = 'en'"
        :class="tab === 'en' ? 'bg-brand-500 text-white' : 'bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'"
        class="px-2.5 py-1 transition-colors">EN</button>
    <button type="button"
        @click="tab = 'bg'"
        :class="tab === 'bg' ? 'bg-brand-500 text-white' : 'bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'"
        class="px-2.5 py-1 border-l border-gray-200 dark:border-gray-700 transition-colors">БГ</button>
</div>
