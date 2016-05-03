<!DOCTYPE html>
<html>
<head><title>Due Contracts</title></head>
<body>
<p>Hello {{ $contract->user->name }}.</p>

<p>Just wanted to let you know that your contract for {{ $contract->name }} is due to expire
    on {{ $contract->ended_at->format('F j, Y') }}, or about {{ $contract->ended_at->diffForHumans() }}.</p>

<p>The noted summary in our database is:</p>

<blockquote>
    <i>{{ $contract->description }}</i>
</blockquote>

<p>Please take your appropriate course of action for this contract.</p>

<p>Thank you,<br/>
    Say Cinema<br/>
    989-720-2667</p>
</body>
</html>