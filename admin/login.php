<?php 
require '../database.php';
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = sha1($_POST['password']);

        $result = $db->table('admin_login')->where(['username' => $username])->first();

        if ($result->password == $password) {
            $response = [
                'status' => true,
                'username' => $result->username
            ];
        } else {
            $response = [
                'status' => false,
                'username' => $result->username
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo "username and password field required";
    }
} else {
    echo "Only POST requests are allowed.";
}

?>