<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: user.php");
    exit;
}
 
// Include config file
//require_once "config/connect.php";
include_once 'config/connect.php';
include_once 'class/stat_user.php';

$database = new Database();
$db = $database->getConn();
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){

        $user_item = new user_connect($db);
        $stmt = $user_item -> getUser(trim($_POST["username"]));
        // Prepare a select statement
        $sql = "SELECT * FROM user WHERE un = :username";
        
        //if($stmt = $db->prepare($sql)){
        if($stmt) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["un"];
                        $pw = $row["pw"];
                        $cp = $row["cp"];
                        $login_err = "Invalid Password!";
                        
                        if($password == $pw){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["cp"] = $cp;                           
                            
                            // Redirect user to welcome page
                            header("location: user.php");
                        } else{
                            // Password is not valid, display a generic error message
                            //$login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Motorcycle Tracker</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="js/scripts.js"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!--
        
        -->

        <style>
            .carousel-item{
                min-height: 280px;
                max-height: 500px;
            }

            .modal-login {
                width: 320px;
            }
            .modal-login .modal-content {
                border-radius: 1px;
                border: none;
            }
            .modal-login .modal-header {
                position: relative;
                justify-content: center;
                background: #f2f2f2;
            }
            .modal-login .modal-body {
                padding: 30px;
            }
            .modal-login .modal-footer {
                background: #f2f2f2;
            }
            .modal-login h4 {
                text-align: center;
                font-size: 26px;
            }
            .modal-login label {
                font-weight: normal;
                font-size: 13px;
            }
            .modal-login .form-control, .modal-login .btn {
                min-height: 38px;
                border-radius: 2px; 
            }
            .modal-login .hint-text {
                text-align: center;
            }
            .modal-login .close {
                position: absolute;
                top: 15px;
                right: 15px;
            }
            .modal-login .btn {
                min-width: 100px;
                background: #3498db;
                border: none;
                line-height: normal;
            }
            .modal-login .btn:hover, .modal-login .btn:focus {
                background: #248bd0;
            }
            .modal-login .hint-text a {
                color: #999;
            }
            .trigger-btn {
                display: inline-block;
                margin: 100px auto;
            }
        </style>

    </head>
<body>

    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Track Your Motorcycle Here!</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    
                    <li class="nav-item"><a class="nav-link" data-toggle="modal" data-target="#myModal" href="#">Login</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">More</a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">How to Use</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item" href="#">Contact</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page content-->
    <div class="container">
        <div class="text-center mt-5">
            <h1>IOT-BASED SMART MOTORCYCLE MANAGEMENT SYSTEM</h1>
            <p class="lead">Anti-theft and Accident Prevention System for your Motorcycle</p>
            
        </div>
    </div>



    <!-- Carousel wrapper -->
<div class="container-lg my-3">
    <div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
        <!-- Carousel indicators -->
        <ol class="carousel-indicators">
            <li data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#myCarousel" data-bs-slide-to="1"></li>
            <li data-bs-target="#myCarousel" data-bs-slide-to="2"></li>
        </ol>
        
        <!-- Wrapper for carousel items -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/assets/1.png" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="/assets/2.jpeg" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="/assets/3.jpg" class="d-block w-100" alt="Slide 3">
            </div>
        </div>

        <!-- Carousel controls -->
        <a class="carousel-control-prev" href="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</div>
<!-- Carousel wrapper -->


<!-- MODAL -->


        <div id="myModal" class="modal fade">
            <div class="modal-dialog modal-login" >
                <div class="modal-content" >
                    <?php 
                        if(!empty($login_err)){
                            echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }        
                    ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="modal-header">				
                            <h4 class="modal-title">Login</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">				
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" required="required" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                            </div>
                            <div class="form-group">
                                <div class="clearfix">
                                    <label>Password</label>
                                    <input type="password" name="password" required="required" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <input type="submit" class="btn btn-primary" value="Login">
                        </div>
                    </form>
                </div>
            </div>
        </div>  
    
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>