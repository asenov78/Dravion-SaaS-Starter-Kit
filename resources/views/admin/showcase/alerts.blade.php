<x-layouts.admin title="Alerts">
    <x-common.page-breadcrumb pageTitle="Alerts" />

    <div class="space-y-5 sm:space-y-6">
        <x-common.component-card title="Success Alert">
            <div class="space-y-4">
                <x-ui.alert variant="success" title="Success Message" message="Be cautious when performing this action." :showLink="true" linkHref="/" linkText="Learn more" />
                <x-ui.alert variant="success" title="Success Message" message="Be cautious when performing this action." :showLink="false" />
            </div>
        </x-common.component-card>

        <x-common.component-card title="Warning Alert">
            <div class="space-y-4">
                <x-ui.alert variant="warning" title="Warning Message" message="Be cautious when performing this action." :showLink="true" linkHref="/" linkText="Learn more" />
                <x-ui.alert variant="warning" title="Warning Message" message="Be cautious when performing this action." :showLink="false" />
            </div>
        </x-common.component-card>

        <x-common.component-card title="Error Alert">
            <div class="space-y-4">
                <x-ui.alert variant="error" title="Error Message" message="Be cautious when performing this action." :showLink="true" linkHref="/" linkText="Learn more" />
                <x-ui.alert variant="error" title="Error Message" message="Be cautious when performing this action." :showLink="false" />
            </div>
        </x-common.component-card>

        <x-common.component-card title="Info Alert">
            <div class="space-y-4">
                <x-ui.alert variant="info" title="Info Message" message="Be cautious when performing this action." :showLink="true" linkHref="/" linkText="Learn more" />
                <x-ui.alert variant="info" title="Info Message" message="Be cautious when performing this action." :showLink="false" />
            </div>
        </x-common.component-card>

        <x-common.component-card title="Alert with Slot Content">
            <x-ui.alert variant="success" title="Custom Content Alert">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This alert uses <strong class="text-gray-900 dark:text-white">custom slot content</strong> instead of the message prop.
                </p>
                <ul class="mt-2 text-sm text-gray-500 dark:text-gray-400 list-disc list-inside">
                    <li>You can add any HTML content</li>
                    <li>Including lists and formatting</li>
                </ul>
            </x-ui.alert>
        </x-common.component-card>
    </div>
</x-layouts.admin>
