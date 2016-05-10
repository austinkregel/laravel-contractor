<!DOCTYPE html>
<html>
<head><title>Due Contracts</title></head>
<body>
<p>Hello {{ $contract->user->name }}.</p>

<p>Just wanted to let you know that there is a new contract, {{ $contract->name }}, is due to expire on {{ $contract->ended_at->format('F j, Y') }}.</p>

<p>The noted summary in our database is:</p>

<blockquote>
    <i>{{ $contract->description }}</i>
</blockquote>

<p>Thank you,<br/>
    Say Cinema<br/>
    989-720-2667</p>
</body>
</html>