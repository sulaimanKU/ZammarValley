<x-mail::message>
{{-- Custom Header/Logo Area --}}
<div style="text-align: center; margin-bottom: 25px;">
    <h1 style="color: #3b82f6; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; margin: 0;">
        ZAMMAR <span style="color: #1e293b;">VALLEY</span>
    </h1>
    <div style="height: 2px; width: 40px; background-color: #3b82f6; margin: 10px auto;"></div>
</div>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
<p style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 15px;">@lang('Hello!')</p>
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<p style="color: #475569; line-height: 1.6;">{{ $line }}</p>
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success' => 'success',
        'error' => 'error',
        default => 'primary', // This uses the color defined in your mail theme
    };
?>
<div style="padding: 20px 0; text-align: center;">
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
</div>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
<p style="color: #475569; line-height: 1.6;">{{ $line }}</p>
@endforeach

{{-- Salutation --}}
<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
@if (! empty($salutation))
<span style="color: #64748b;">{{ $salutation }}</span>
@else
<p style="color: #64748b; margin: 0;">@lang('Regards,')</p>
<strong style="color: #1e293b;">{{ config('app.name') }} Team</strong>
@endif
</div>

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
<div style="font-size: 12px; color: #94a3b8;">
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your browser:",
    [
        'actionText' => $actionText,
    ]
)
<br>
<span class="break-all" style="color: #3b82f6;">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</div>
</x-slot:subcopy>
@endisset
</x-mail::message>
