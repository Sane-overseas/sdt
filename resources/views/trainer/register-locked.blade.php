<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Locked - SOPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card mx-auto" style="max-width:560px;">
        <div class="card-body text-center p-4">
            <h4>Form Locked</h4>
            <p class="text-muted">{{ $message }}</p>
            @if(!empty($registration))
                <p><strong>Status:</strong> {{ ucfirst($registration->status) }}</p>
            @endif
            <a href="{{ route('trainer.register') }}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div>
</body>
</html>
