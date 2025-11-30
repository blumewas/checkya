<x-mail::message>
{{ __('notifications.report.intro', ['name' => $name]) }}

# Report

@empty($expectations)
{{ __('notifications.report.no_results') }}
@else
<x-mail::table>
| {{ __('notifications.report.table.assertion') }} | {{ __('notifications.report.table.actual_value') }} | {{ __('notifications.report.table.result') }} |
| --------- | ----- | -------- |
@foreach ($expectations as $assertion => $result)
| {{ $assertion }} | {{ $result['actual'] ?? '-' }} | {{ ($result['result'] ?? false) ? __('notifications.report.table.success') : __('notifications.report.table.failed') }} |
@endforeach
</x-mail::table>
@endempty

<x-mail::button url="{{ route('filament.admin.resources.api-suites.view', ['record' => $id]) }}">
{{ __('notifications.report.action.view') }}
</x-mail::button>

{{ __('notifications.report.thanks') }}
<br>
{{ config('app.name') }}
</x-mail::message>
