<?php
function executePythonScript($imagePath)
{
    $pythonScript = "python script.py";
    putenv("PYTHONIOENCODING=utf-8");

    $command = "{$pythonScript} {$imagePath}";
    $output = shell_exec($command);

    $lines = explode("\n", $output);
    $relevant_output = "";
    foreach ($lines as $line) {
        if (strpos($line, '{') !== false) {
            $relevant_output .= $line;
        }
    }

    $decoded_output = json_decode(stripslashes($relevant_output), true);

    $json_response = json_encode($decoded_output);

    return $json_response;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) {
        $uploadDirectory = __DIR__ . "/uploads/";

        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $uploadFile = $uploadDirectory . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $response = executePythonScript($uploadFile);
            header('Content-Type: application/json');
            echo $response;
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "No image file uploaded.";
    }
} else {
    echo "Only POST requests are allowed.";
}
?>
