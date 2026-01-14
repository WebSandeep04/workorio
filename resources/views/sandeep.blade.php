<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Builder Component View</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      height: 100vh;
      margin: 0;
    }
    header {
      background: #fff;
      border-bottom: 1px solid #dee2e6;
      padding: 10px 20px;
    }
    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    x-form-builder {
      display: block;
      width: 100%;
      max-width: 900px;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 15px;
    }
  </style>
</head>
<body>
  <header class="d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Form Builder Component Viewer</h5>
  </header>

  <main>
    <x-form-builder id="2"></x-form-builder>
  </main>
</body>
</html>
