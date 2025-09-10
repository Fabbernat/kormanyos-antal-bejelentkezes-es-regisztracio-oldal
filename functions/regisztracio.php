<?php
session_start();

include "fajlmuveletek.php";
$fiokok = load_users("../json/users.json");

$hibak = [];

//if (isset($_POST["regiszt"])) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST["full-name"]) || trim($_POST["full-name"]) === "") {
        $hibak[] = "A teljes név megadása kötelező!";
    }

    if (!isset($_POST["username"]) || trim($_POST["username"]) === "") {
        $hibak[] = "A felhasználónév megadása kötelező!";
    }

    if (!isset($_POST["passwd"]) || trim($_POST["passwd"]) === "" || !isset($_POST["passwd-check"]) || trim($_POST["passwd-check"]) === "") {
        $hibak[] = "A jelszó és az ellenőrző jelszó megadása kötelező!";
    }

    if (!isset($_POST["email"]) || trim($_POST["email"]) === "") {
        $hibak[] = "Az email-cím megadása kötelező!";
    }
    elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $hibak[] = "Az email-cím formátuma helytelen!";
    }

    $teljes_nev  = $_POST["full-name"];
    $felhasznalonev = $_POST["username"];
    $jelszo = $_POST["passwd"];
    $jelszo2 = $_POST["passwd-check"];
    $email = $_POST["email"];

    // Check if username already exists
    foreach ($fiokok["users"] as $fiok) {
        if (isset($fiok["username"]) && $fiok["username"] === $felhasznalonev) {
            $hibak[] = "A felhasználónév már foglalt!";
            break; // Exit the loop once a matching username is found
        }
    }

    if (strlen($jelszo) < 5)
        $hibak[] = "A jelszónak legalább 5 karakter hosszúnak kell lennie!";

    if ($jelszo !== $jelszo2)
        $hibak[] = "A jelszó és az ellenőrző jelszó nem egyezik!";

    if (count($hibak) === 0) {   // Successful registration
        $hashed_password = password_hash($jelszo, PASSWORD_DEFAULT);
        $fiok = [
            "full-name" => $teljes_nev,
            "username" => $felhasznalonev,
            "password" => $hashed_password,
            "email" => $email
        ];
        // Append the new user to the existing array
        $fiokok[] = $fiok;
        // Save the updated array back to the JSON file

        $json_data = json_encode($fiokok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        save_users("../json/users.json", $json_data);
        header("Location: ../signup.php?success=true");
        exit();
    }
}

// If there are errors, redirect back to signup.php with error messages
$errors_query = http_build_query(array("errors" => $hibak));
header("Location: ../signup.php?$errors_query");
exit();