@component('mail::message')
# Report Resolved

Hello {{ UCFirst($report->user->first_name) }} {{ UCFirst($report->user->last_name) }},

Your report has on post "{{ $report->post->title }}" has been resolved.

Thank you for your patience and understanding.
Best regards,

The Admin Team

@endcomponent
