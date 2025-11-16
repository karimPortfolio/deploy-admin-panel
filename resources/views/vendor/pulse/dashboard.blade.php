<x-pulse>
    <livewire:pulse.servers cols="full" ignore-after="3 hours" />

    <livewire:pulse.usage cols="4" rows="2" />

    <livewire:pulse.queues cols="4" />

    <livewire:pulse.cache cols="4" />

    <livewire:pulse.slow-queries cols="8" />

    <livewire:pulse.exceptions cols="6" />

    <livewire:pulse.slow-requests cols="6" />

    <livewire:pulse.slow-jobs cols="6" />

    <livewire:pulse.slow-outgoing-requests cols="6" />
    <div class="text-gray-600 dark:text-gray-400 col-span-full" >
        N.B: {{ __('messages.monitoring_msg') }}
    </div>
</x-pulse>
@persist('css')
<style>
    body div > header {
        display: none !important;
    }
</style>
@endpersist