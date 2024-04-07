<?php

require '../database.php';
$db = new Database();

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
        if (isset($_POST['name']) && isset($_POST['email'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];

            $uploadDirectory = "../uploads/";

            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $file = 'image_' . mt_rand() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $uploadFile = $uploadDirectory . $file; 

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $response = executePythonScript($uploadFile);
                $result = json_decode($response);
                
                $db->table('user_predicition')->insert(['name' => $name, 'email' => $email, 'image' => $file, 'predicted_disease' => $result->predicted_disease, 'probability' =>$result->probability, 'description' => $result->description]);
                header('Content-Type: application/json');
                echo $response;
            } else {
                echo "Failed to upload file.";
            }
        } else {
            echo "Email and name field required";
        }
    } else {
        echo "No image file uploaded.";
    }
} else {
    echo "Only POST requests are allowed.";
}
