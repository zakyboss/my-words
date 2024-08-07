<?php
session_start();
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signup'])) {
        $fname = htmlspecialchars($_POST['fname']);
        $lname = htmlspecialchars($_POST['lname']);
        $username = htmlspecialchars($_POST['username']);
        $password = $_POST['pass'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($username) && !empty($password) && !empty($fname) && !empty($lname)) {
            $profilePic = '';
            if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == UPLOAD_ERR_OK) {
                $profilePic = uniqid() . '_' . basename($_FILES['profilePic']['name']);
                move_uploaded_file($_FILES['profilePic']['tmp_name'], 'Uploads/' . $profilePic);
            }

            $stmt = $con->prepare("INSERT INTO formdetails (username, pass, profilePic, fname, lname) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $hashed_password, $profilePic, $fname, $lname);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "<script>alert('Please fill in all fields.');</script>";
        }
    } elseif (isset($_POST['login'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = $_POST['password'];

        if (!empty($username) && !empty($password)) {
            $stmt = $con->prepare("SELECT pass, profilePic, fname, lname FROM formdetails WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hash, $profilePic, $firstname, $lastname);
                $stmt->fetch();

                if (password_verify($password, $hash)) {
                    $_SESSION['username'] = $username;
                    $_SESSION['user'] = [
                        'username' => $username,
                        'firstName' => $firstname,
                        'lastName' => $lastname,
                        'profilePic' => $profilePic // Store profilePic in session
                    ];
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Login Successful',
                                    text: 'Welcome, $firstname $lastname!',
                                    confirmButtonText: 'OK'
                                }).then(function() {
                                    window.location.href = 'home.php';
                                });
                            });
                          </script>";
                    $stmt->close();
                } else {
                    echo "<script>alert('Invalid username or password.');</script>";
                }
            } else {
                echo "<script>alert('Invalid username or password.');</script>";
            }
        } else {
            echo "<script>alert('Please fill in both fields.');</script>";
        }
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AgriNurture Login</title>
    <link rel="stylesheet" href="index.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #81C408 0%, #B3FFAB 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .profile-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            background-color: #81C408;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #68A005;
        }

        .cancelbtn {
            background-color: #f44336;
            margin: 10px 0;
        }

        .account-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .account-info button, .account-info a {
            width: auto;
            padding: 10px 20px;
            margin-left: 10px;
            background-color: transparent;
            color: #81C408;
            border: 2px solid #81C408;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .account-info button:hover, .account-info a:hover {
            background-color: #81C408;
            color: white;
        }

        .signup, .login {
            display: none;
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <div class="login">
        <div class="profile-preview">
            <img src="Pics/download.jpg" alt="logo">
        </div>
        <div class="container">
            <form action="" method="POST">
                <input type="hidden" name="login" value="1">
                <label for="username">Username:</label>
                <input type="text" placeholder="Enter Username" name="username" required/>
                <label for="password">Password:</label>
                <input type="password" placeholder="Enter Password" name="password" required/>
                <button type="submit">Login</button>
                <input type="checkbox" checked="checked"/> Remember me

                <button type="button" class="cancelbtn">Cancel</button>
                Forgot <a href="passwordReset.php">password?</a>

                <div class="account-info">
                    <h4>Don't Have An Account?</h4>
                    <button type="button" onclick="toggleSignUp()">SIGNUP</button>
                </div>
            </form>
        </div>
    </div>

    <div class="signup">
        <div class="profile-preview">
            <img id="profileImage" src="Pics/download.jpg" alt="profile" style="margin-top: 40px;">
        </div>
        <div class="container" style="margin-top: 270px;">
            <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateSignup()">
                <input type="hidden" name="signup" value="1">
                <h2>Please Signup With Us!</h2>
                <label for="firstName">First Name:</label>
                <input type="text" placeholder="Enter Your First Name" name="fname" required id="firstName"/>
                <label for="lastName">Last Name:</label>
                <input type="text" placeholder="Enter Your Last Name" name="lname" required id="lastName"/>
                <label for="newUsername">Create Email:</label>
                <input type="text" placeholder="Enter valid Email Address" name="username" required id="newUsername"/>
                <label for="newPassword">Create Password:</label>
                <input type="password" placeholder="Enter Password" name="pass" required id="newPassword"/>
                <label for="confirmNewPassword">Confirm Password:</label>
                <input type="password" placeholder="Confirm Password" name="confirmpass" required id="confirmNewPassword"/>
                <label for="profile">Profile Picture:</label>
                <input type="file" name="profilePic" id="profile" onchange="previewProfilePicture(event)"/>
                <button type="submit">Signup</button>
                <input type="checkbox" checked="checked"/> Remember me
                <button type="button" class="cancelbtn">Cancel</button>
                Forgot <a href="passwordReset.php">password?</a>
                <div class="account-info">
                    <h4>Have An Account?</h4>
                    <button type="button" onclick="toggleSignUp()">LOGIN</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector('.login').style.display = 'block';
        });

        function toggleSignUp() {
            const signUpSection = document.querySelector('.signup');
            const loginSection = document.querySelector('.login');

            if (signUpSection.style.display === 'block') {
                signUpSection.style.display = 'none';
                loginSection.style.display = 'block';
            } else {
                signUpSection.style.display = 'block';
                loginSection.style.display = 'none';
            }
        }

        function previewProfilePicture(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImage');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function validateSignup() {
            let newUsername = document.getElementById("newUsername").value;
            let newPassword = document.getElementById("newPassword").value;
            let confirmNewPassword = document.getElementById("confirmNewPassword").value;

            if (newUsername.trim() === "") {
                alert("Please enter a username.");
                return false;
            }
            if (!newUsername.includes(".") || !newUsername.includes("@")) {
                alert("Please enter a valid email.");
                return false;
            }
            if (newPassword.trim() === "") {
                alert("Please enter a password.");
                return false;
            }
            if (newPassword !== confirmNewPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
