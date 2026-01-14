@php
    $formName = $form->name;
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $formName }}</title>
    <link rel="icon" href="data:,">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .fb-embed-shell {
            width: 100%;
            max-width: 760px;
            padding: 1rem;
        }
        .fb-embed-card {
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            box-shadow: none;
            overflow: hidden;
        }
        .fb-embed-header {
            background: #434afa;
            color: #fff;
            padding: 0.75rem 1rem;
        }
        .fb-embed-header h1 {
            font-size: 1.1rem;
            margin: 0;
            font-weight: 500;
        }
        .fb-embed-body {
            background: #ffffff;
            padding: 1.5rem;
        }
        .fb-embed-body .form-label {
            font-weight: 500;
            color: #334155;
            font-size: 0.9rem;
        }
        .fb-embed-body .form-control {
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            padding: .5rem .7rem;
            font-size: 0.9rem;
            transition: border-color .2s ease;
        }
        .fb-embed-body .form-control:focus {
            border-color: #434afa;
            box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
        }
        .fb-embed-body .btn-primary {
            background: #434afa;
            border: none;
            border-radius: 3px;
            padding: .5rem 1.5rem;
            font-weight: 500;
            box-shadow: none;
        }
        .fb-embed-body .btn-primary:hover {
            background: #3238c9;
        }
        .fb-embed-footer {
            text-align: center;
            font-size: .85rem;
            color: #64748b;
            margin-top: 1rem;
        }
        @media (max-width: 575.98px) {
            .fb-embed-body { padding: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="fb-embed-shell">
        <div class="card fb-embed-card">
            <div class="fb-embed-header">
                <h1>{{ $formName }}</h1>
                <div class="small text-white-50">Fill in the details below</div>
            </div>
            <div class="fb-embed-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-form-builder :id="$form->id" embed :action="$submitUrl ?? null" />
            </div>
        </div>
        <div class="fb-embed-footer">Powered by Workorio</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

