
<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";
include("control.php");

// Define variables and initialize with empty values
$client_fullname = $id_number = $phone_number = $ministry = $type = "";
$client_fullname_err = $id_number_err = $phone_number_err = $ministry_err = $type_err = "";
$pf_number = $amount = $comment = $pf_number_err = $amount_err = $comment_err = "";
$pf_number_conversion = $password = $amount_applied = $date_field = $comment_conversion = "";
$pf_number_conversion_err = $password_err = $amount_applied_err = $date_field_err = $comment_conversion_err = "";
$type_loan_qualify = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate client full name
    if (empty(trim($_POST["client_fullname"]))) {
        $client_fullname_err = "Please enter client full name.";
    } else {
        $client_fullname = trim($_POST["client_fullname"]);
    }

    // Validate ID number
    if (empty(trim($_POST["id_number"]))) {
        $id_number_err = "Please enter ID number.";
    } else {
        $id_number = trim($_POST["id_number"]);

        // Check if the ID number already exists in the database
        $sql_check_duplicate = "SELECT id FROM clients WHERE id_number = ?";
        if ($stmt_check_duplicate = mysqli_prepare($link, $sql_check_duplicate)) {
            mysqli_stmt_bind_param($stmt_check_duplicate, "s", $id_number);
            if (mysqli_stmt_execute($stmt_check_duplicate)) {
                mysqli_stmt_store_result($stmt_check_duplicate);

                if (mysqli_stmt_num_rows($stmt_check_duplicate) > 0) {
                    $id_number_err = "Client with this ID number already exists.";
                }
            } else {
                echo '<script>alert("Oops! Something went wrong. Please try again later.");</script>';
            }

            mysqli_stmt_close($stmt_check_duplicate);
        }
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter phone number.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Validate ministry
    if (empty(trim($_POST["ministry"]))) {
        $ministry_err = "Please enter ministry.";
    } else {
        $ministry = trim($_POST["ministry"]);
    }

    // Validate type
    if (empty($_POST["type"])) {
        $type_err = "Please select a type.";
    } else {
        $type = $_POST["type"];

        // Check if "Lead" radio button is selected
        if ($_POST["type"] === "lead") {
            // Validate additional fields for Lead
            if (empty(trim($_POST["pf_number"]))) {
                $pf_number_err = "Please enter PF number for Lead.";
            } else {
                $pf_number = trim($_POST["pf_number"]);
            }

            if (empty(trim($_POST["amount"]))) {
                $amount_err = "Please enter amount for Lead.";
            } else {
                $amount = trim($_POST["amount"]);
            }

            $comment = trim($_POST["comment"]);
        }

        // Check if "Conversion" radio button is selected
        if ($_POST["type"] === "conversion") {
            // Validate additional fields for Conversion
            if (empty(trim($_POST["pf_number_conversion"]))) {
                $pf_number_conversion_err = "Please enter PF number for Conversion.";
            } else {
                $pf_number_conversion = trim($_POST["pf_number_conversion"]);
            }

            if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter password for Conversion.";
            } else {
                $password = trim($_POST["password"]);
            }

            if (empty(trim($_POST["amount_applied"]))) {
                $amount_applied_err = "Please enter amount applied for Conversion.";
            } else {
                $amount_applied = trim($_POST["amount_applied"]);
            }

            if (empty(trim($_POST["date_field"]))) {
                $date_field_err = "Please enter a date for Conversion.";
            } else {
                $date_field = trim($_POST["date_field"]);
            }

            $comment_conversion = trim($_POST["comment_conversion"]);
        }

        // Dropdown for type of loan qualification
        $type_loan_qualify = $_POST["type_loan_qualify"];
    }

    if (empty($client_fullname_err) && empty($id_number_err) && empty($phone_number_err) && empty($ministry_err) && empty($type_err)) {
        // Establish a database connection (update with your database credentials)
        $link = mysqli_connect("localhost", "root", "", "acapp");

        if (!$link) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prepare an insert statement
        $sql = "INSERT INTO clients (client_fullname, id_number, phone_number, ministry, type, pf_number, amount, comment, pf_number_conversion, password, amount_applied, date_field, comment_conversion, type_loan_qualify) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Determine the selected type
        if ($_POST["type"] === "lead") {
            $type_str = "lead";
        } elseif ($_POST["type"] === "conversion") {
            $type_str = "conversion";
        } else {
            $type_str = "prospects"; // Default value if none is selected
        }

        try {
            // Attempt to execute the prepared statement
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Define the type definition string based on the number of variables you're binding
                $types = "ssssssssssssss"; // Adjust this string based on your variables

                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, $types, $client_fullname, $id_number, $phone_number, $ministry, $type_str, $pf_number, $amount, $comment, $pf_number_conversion, $password, $amount_applied, $date_field, $comment_conversion, $type_loan_qualify);

                if (mysqli_stmt_execute($stmt)) {
                    
                    // Data inserted successfully
                    echo '<script>alert("Client added successfully.");</script>';
                    // Clear form fields
                    $client_fullname = $id_number = $phone_number = $ministry = $pf_number = $amount = $comment = $pf_number_conversion = $password = $amount_applied = $date_field = $comment_conversion = $type_loan_qualify = "";
                } else {
                    echo '<script>alert("Oops! Something went wrong. Please try again later.");</script>';
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                // MySQL error code 1062 represents a duplicate entry error
                echo '<script>alert("Client with this ID number already exists.");</script>';
            } else {
                // Handle other database errors if needed
                echo '<script>alert("Database error. Please try again later.");</script>';
            }
        }

        // Close the database connection
        mysqli_close($link);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>Matunda AC Agents</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- site icon -->
      <link rel="icon" href="images/fevicon.png" type="image/png" />
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css" />
      <!-- site css -->
      <link rel="stylesheet" href="style.css" />
      <style>
      
    </style>
      <!-- responsive css -->
      <link rel="stylesheet" href="css/responsive.css" />
      <!-- color css -->
      <link rel="stylesheet" href="css/colors.css" />
      <!-- select bootstrap -->
      <link rel="stylesheet" href="css/bootstrap-select.css" />
      <!-- scrollbar css -->
      <link rel="stylesheet" href="css/perfect-scrollbar.css" />
      <!-- custom css -->
      <link rel="stylesheet" href="css/custom.css" />
      <!-- calendar file css -->
      <link rel="stylesheet" href="js/semantic.min.css" />
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body class="inner_page price_table">
      <div class="full_container">
         <div class="inner_container">
            <!-- Sidebar  -->
            <nav id="sidebar">
               <div class="sidebar_blog_1">
                  <div class="sidebar-header">
                     <div class="logo_section">
                        <a href="index.html"><img class="logo_icon img-responsive" src="images/logo/logo_icon.png" alt="#" /></a>
                     </div>
                  </div>
                  <div class="sidebar_user_info">
                     <div class="icon_setting"></div>
                     <div class="user_profle_side">
                        <div class="user_img"><img class="img-responsive" src="images/layout_img/user_img.jpg" alt="#" /></div>
                        <div class="user_info">
                           <h6><?php echo $full_name; ?></h6>
                           <p><span class="online_animation"></span> Online</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="sidebar_blog_2">
                  <h4>General</h4>
                  <ul class="list-unstyled components">
                     <li class="active">
                        <a href="#dashboard" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-dashboard yellow_color"></i> <span>Dashboard</span></a>
                        <ul class="collapse list-unstyled" id="dashboard">
                           <li>
                              <a href="dashboard.php">> <span>Dashboard</span></a>
                           </li>
                           
                        </ul>
                     </li>
                     
                     
                     
            </nav>
            <!-- end sidebar -->
            <!-- right content -->
            <div id="content">
               <!-- topbar -->
               <div class="topbar">
                  <nav class="navbar navbar-expand-lg navbar-light">
                     <div class="full">
                        <button type="button" id="sidebarCollapse" class="sidebar_toggle"><i class="fa fa-bars"></i></button>
                        <div class="logo_section">
                           <a href="index.html"><img class="img-responsive" src="images/logo/logo.png" alt="#" /></a>
                        </div>
                        <div class="right_topbar">
                           <div class="icon_info">
                              <ul>
                                 <li><a href="#"><i class="fa fa-bell-o"></i><span class="badge">2</span></a></li>
                                 <li><a href="#"><i class="fa fa-question-circle"></i></a></li>
                                 <li><a href="#"><i class="fa fa-envelope-o"></i><span class="badge">3</span></a></li>
                              </ul>
                              <ul class="user_profile_dd">
                                 <li>
                                    <a class="dropdown-toggle" data-toggle="dropdown"><img class="img-responsive rounded-circle" src="images/layout_img/user_img.jpg" alt="#" /><span class="name_user"><?php echo $full_name; ?></span></a>
                                    <div class="dropdown-menu">
                                       <a class="dropdown-item" href="profile.php">My Profile</a>
                                       <a class="dropdown-item" href="settings.html">Settings</a>
                                       <a class="dropdown-item" href="help.html">Help</a>
                                       <a class="dropdown-item" href="#"><span>Log Out</span> <i class="fa fa-sign-out"></i></a>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </nav>
               </div>
               <!-- end topbar -->
               <!-- dashboard inner -->
               <div class="midde_cont">
                  <div class="container-fluid">
                     <div class="row column_title">
                        <div class="col-md-12">
                     <div class="page_title">
                 <div class="wrapper" style="width: 50%; margin: 0 auto;">
                <h2 class="text-center">Add Client</h2> 
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Client Full Name</label>
                <input type="text" name="client_fullname" class="form-control <?php echo (!empty($client_fullname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $client_fullname; ?>">
                <span class="invalid-feedback"><?php echo $client_fullname_err; ?></span>
            </div>
            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_number" class="form-control <?php echo (!empty($id_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $id_number; ?>">
                <span class="invalid-feedback"><?php echo $id_number_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control <?php echo (!empty($phone_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone_number; ?>">
                <span class="invalid-feedback"><?php echo $phone_number_err; ?></span>
            </div>
            <div class="form-group">
                <label>Ministry</label>
                <input type="text" name="ministry" class="form-control <?php echo (!empty($ministry_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ministry; ?>">
                <span class="invalid-feedback"><?php echo $ministry_err; ?></span>
            </div>
            <div class="form-group">
                <label>Type</label><br>
                <input type="radio" name="type" value="prospects"> Prospects
                <input type="radio" name="type" value="lead"> Lead
                <input type="radio" name="type" value="conversion"> Conversion
                <span class="invalid-feedback"><?php echo $type_err; ?></span>
            </div>

            <!-- Additional fields for Lead -->
            <div id="lead-fields" style="display:none;">
                <div class="form-group">
                    <label>PF Number (Lead)</label>
                    <input type="text" name="pf_number" class="form-control <?php echo (!empty($pf_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $pf_number; ?>">
                    <span class="invalid-feedback"><?php echo $pf_number_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Amount (Lead)</label>
                    <input type="text" name="amount" class="form-control <?php echo (!empty($amount_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $amount; ?>">
                    <span class="invalid-feedback"><?php echo $amount_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Comment (Lead)</label>
                    <textarea name="comment" class="form-control"><?php echo $comment; ?></textarea>
                </div>
            </div>

            <!-- Additional fields for Conversion -->
            <div id="conversion-fields" style="display:none;">
                <div class="form-group">
                    <label>PF Number (Conversion)</label>
                    <input type="text" name="pf_number_conversion" class="form-control <?php echo (!empty($pf_number_conversion_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $pf_number_conversion; ?>">
                    <span class="invalid-feedback"><?php echo $pf_number_conversion_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password (Conversion)</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Amount Applied (Conversion)</label>
                    <input type="text" name="amount_applied" class="form-control <?php echo (!empty($amount_applied_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $amount_applied; ?>">
                    <span class="invalid-feedback"><?php echo $amount_applied_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Date (Conversion)</label>
                    <input type="date" name="date_field" class="form-control <?php echo (!empty($date_field_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $date_field; ?>">
                    <span class="invalid-feedback"><?php echo $date_field_err; ?></span>
                </div>
                <div class="form-group">
                        <label>Type of Loan Qualify</label>
                        <select name="type_loan_qualify" class="form-control">
                            <option value="refinance">Refinance</option>
                            <option value="topup">Top-Up</option>
                            <option value="buyoff">Buy-Off</option>
                        </select>
                    </div>
                <div class="form-group">
                    <label>Comment (Conversion)</label>
                    <textarea name="comment_conversion" class="form-control"><?php echo $comment_conversion; ?></textarea>
                </div>
            </div>

            

            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Add Client">
            </div>
        </form>
    </div>

    <script>
        // JavaScript to show/hide Lead and Conversion fields based on radio button selection
        const leadRadio = document.querySelector('input[name="type"][value="lead"]');
        const conversionRadio = document.querySelector('input[name="type"][value="conversion"]');
        const leadFields = document.getElementById('lead-fields');
        const conversionFields = document.getElementById('conversion-fields');

        leadRadio.addEventListener('change', function () {
            if (this.checked) {
                leadFields.style.display = 'block';
                conversionFields.style.display = 'none';
            } else {
                leadFields.style.display = 'none';
            }
        });

        conversionRadio.addEventListener('change', function () {
            if (this.checked) {
                conversionFields.style.display = 'block';
                leadFields.style.display = 'none';
            } else {
                conversionFields.style.display = 'none';
            }
        });
    </script>
            </div>
         </div>
      </div>
      <!-- jQuery -->
      <script src="js/jquery.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.min.js"></script>
      <!-- wow animation -->
      <script src="js/animate.js"></script>
      <!-- select country -->
      <script src="js/bootstrap-select.js"></script>
      <!-- owl carousel -->
      <script src="js/owl.carousel.js"></script> 
      <!-- chart js -->
      <script src="js/Chart.min.js"></script>
      <script src="js/Chart.bundle.min.js"></script>
      <script src="js/utils.js"></script>
      <script src="js/analyser.js"></script>
      <!-- nice scrollbar -->
      <script src="js/perfect-scrollbar.min.js"></script>
      <script>
         var ps = new PerfectScrollbar('#sidebar');
      </script>
      <!-- custom js -->
      <script src="js/custom.js"></script>
      <!-- calendar file css -->    
      <script src="js/semantic.min.js"></script>
      <script></script>
   </body>
</html>