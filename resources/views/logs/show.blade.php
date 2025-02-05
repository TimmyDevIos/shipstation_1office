<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webhook Logs</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery (cần thiết cho Bootstrap 4 JavaScript) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Bootstrap 4 JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Webhook Logs</h2>
        <div id="logAccordion" role="tablist" aria-multiselectable="true">
            @foreach($combinedLogs as $index => $log)
                <div class="card">
                    <div class="card-header" id="heading{{ $index }}" role="tab">
                        <h2 class="mb-0">
                            <a class="btn btn-block text-left collapsed" data-toggle="collapse" href="#collapse{{ $index }}" aria-expanded="{{ $index === count($combinedLogs) - 1 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                Log Entry #{{ $index + 1 }}
                            </a>
                        </h2>
                    </div>

                    <div id="collapse{{ $index }}" class="collapse {{ $index === count($combinedLogs) - 1 ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading{{ $index }}" data-parent="#logAccordion">
                        <div class="card-body">
                            <pre>{{ $log }}</pre>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>