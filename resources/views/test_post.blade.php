<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test POST Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Server POST Test</h1>
        <p class="mb-4">Clicking this button will send a POST request to /test.</p>
        <form action="{{ route('test.post') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700">
                Test POST
            </button>
        </form>
    </div>
</body>
</html>
