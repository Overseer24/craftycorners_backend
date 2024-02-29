{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <title>Mentorship Application Status</title>--}}
{{--    <style>--}}
{{--        /* Styles for the email content */--}}
{{--        body {--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            background-color: #f4f4f4;--}}
{{--            margin: 0;--}}
{{--            padding: 0;--}}
{{--            color: #333;--}}
{{--        }--}}

{{--        .container {--}}
{{--            border-color: #0c214e;--}}
{{--            max-width: 600px;--}}
{{--            margin: 0 auto;--}}
{{--            padding: 20px;--}}
{{--            background-color: #fff;--}}
{{--            border-radius: 5px;--}}
{{--            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);--}}
{{--        }--}}

{{--        h1 {--}}
{{--            color: #333;--}}
{{--        }--}}

{{--        p {--}}
{{--            margin-bottom: 20px;--}}
{{--        }--}}

{{--        .status-message {--}}
{{--            font-weight: bold;--}}
{{--            margin-top: 30px;--}}
{{--        }--}}

{{--        .button {--}}
{{--            display: inline-block;--}}
{{--            padding: 10px 20px;--}}
{{--            background-color: #007bff;--}}
{{--            color: #fff;--}}
{{--            text-decoration: none;--}}
{{--            border-radius: 5px;--}}
{{--        }--}}

{{--        .button:hover {--}}
{{--            background-color: #0056b3;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="container">--}}
{{--    <h1>Mentorship Application Status</h1>--}}
{{--    <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>--}}
{{--    <p>Your mentorship application status has been updated.</p>--}}
{{--    <p class="status-message">Status: {{ $status }}</p>--}}
{{--    <p>Thank you for your interest in becoming a mentor.</p>--}}
{{--    <p>If you have any questions, feel free to contact us.</p>--}}
{{--    <p>Best regards,</p>--}}
{{--    <p>The Admin Team</p>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}


@component('mail::message')
# Mentorship Application Status

Hello **{{ ucfirst($mentor->user->first_name) }} {{ ucfirst($mentor->user->last_name) }}**,

@if ($status === 'approved')
Congratulations! Your mentorship application has been **approved**.
@elseif ($status === 'rejected')
We regret to inform you that your mentorship application has been **rejected**.
@endif

Thank you for your interest in becoming a mentor. If you have any questions, feel free to contact us.

Best regards,<br>
The Admin Team
@endcomponent


