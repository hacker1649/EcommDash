<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================d
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Edit User - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="./public/assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="./public/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="./public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="./public/assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="./public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <link rel="stylesheet" href="./public/assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="./public/assets/vendor/js/helpers.js"></script>

  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="./public/assets/js/config.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .error {
      color: #ff0000;
    }

    .hide {
      display: none;
    }
  </style>
</head>

<body>

  <?php

  include "./db_connection/connection.php"; // Include the connection file

  session_start(); // Start session to store data temporarily 

  // Check if the user is already logged in
  if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
    // Redirect to dashboard if user is already logged in
    header("Location: ./user/dashboard.php");
    exit;
  }

  // Check if the user is logged in
  if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit;
  } else {
    $username_input = $_SESSION['username'];
    $email_input = $_SESSION['email'];
  }

  // Check and set user_id in session if passed in the URL
  if (isset($_GET['id'])) {
    $_SESSION['user_id'] = $_GET['id']; // Store user_id in session
  }

  // Retrieve user_id from session
  $user_id = $_SESSION['user_id'] ?? ""; // Use null coalescing operator to avoid undefined variable errors

  $query = "SELECT * FROM tbl_user as u INNER JOIN tbl_user_profile as up ON u.user_id = up.user_id WHERE u.user_id = '$user_id'";
  $result = $conn->query($query);
  $user = $result->fetch_assoc();

  // Define variables and initialize with user data
  $firstName = $user['first_name'];
  $lastName = $user['last_name'];
  $age = $user['age'];
  $gender = $user['gender'];
  $email = $user['user_email'];
  $password = $user['user_password'];
  $phone = $user['phone'];
  $profession = $user['profession'];
  $degree = $user['degree'];
  $major = $user['major'];
  $institute = $user['institute'];
  $address = $user['address'];
  $city = $user['city'];
  $state = $user['state'];
  $country = $user['country'];
  $comment = $user['comments'];
  $profilePhoto = $user['file_name'];

  // Validate form fields after submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profilephoto']) && $_POST['update_profilephoto'] === 'on') {
      if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === 0) {
        $file = $_FILES['profilePhoto'];

        // Check if the file is an image (JPEG, PNG only)
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
          $fileErr = "Only JPEG, JPG and PNG files are allowed.";
        }
      } else {
        $fileErr = "Profile photo is required.";
      }
    }

    if (empty($_POST["first_name"])) {
      $firstNameErr = "First name is required";
    } else {
      $firstName = test_input($_POST["first_name"]);
      if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
        $firstNameErr = "Only letters and white space allowed";
      }
    }

    if (empty($_POST["last_name"])) {
      $lastNameErr = "Last name is required";
    } else {
      $lastName = test_input($_POST["last_name"]);
      if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
        $lastNameErr = "Only letters and white space allowed";
      }
    }

    if (empty($_POST["age"])) {
      $ageErr = "Age is required";
    } else {
      $age = test_input($_POST["age"]);
      if (!is_numeric($age) || $age <= 0 || $age > 100) {
        $ageErr = "Please enter a valid age";
      }
    }

    if (empty($_POST["gender"])) {
      $genderErr = "Gender is required";
    } else {
      $gender = test_input($_POST["gender"]);
    }

    if (empty($_POST["phone"])) {
      $phoneErr = "Phone number is required";
    } else {
      $phone = test_input($_POST["phone"]);
      if (!preg_match('/^\+?[0-9]{1,3}[-\s]?(\(?[0-9]{1,4}\)?[-\s]?)[0-9]{1,4}[-\s]?[0-9]{1,4}$/', $phone)) {
        $phoneErr = "Invalid Phone number!!";
      }
    }

    if (empty($_POST["profession"])) {
      $professionErr = "Profession is required";
    } else {
      $profession = test_input($_POST["profession"]);
    }

    if (empty($_POST["degree"])) {
      $degreeErr = "Degree is required";
    } else {
      $degree = test_input($_POST["degree"]);
    }

    if (empty($_POST["major"])) {
      $majorErr = "Major is required";
    } else {
      $major = test_input($_POST["major"]);
    }

    if (empty($_POST["institute"])) {
      $instituteErr = "Institute is required";
    } else {
      $institute = test_input($_POST["institute"]);
    }

    if (empty($_POST["address"])) {
      $addressErr = "Address is required";
    } else {
      $address = test_input($_POST["address"]);
    }

    if (empty($_POST["city"])) {
      $cityErr = "City is required";
    } else {
      $city = test_input($_POST["city"]);
    }

    if (empty($_POST["state"])) {
      $stateErr = "State is required";
    } else {
      $state = test_input($_POST["state"]);
    }

    if (empty($_POST["country"])) {
      $countryErr = "Country is required";
    } else {
      $country = test_input($_POST["country"]);
    }

    if (empty($_POST["comment"])) {
      $commentErr = "Comments are required";
    } else {
      $comment = test_input($_POST["comment"]);
    }

    if (empty($_POST["useremail"])) {
      $emailErr = "Email is required";
    } else {
      $email = test_input($_POST["useremail"]);

      // Validate email format
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
      }
    }

    if (isset($_POST['update_password']) && $_POST['update_password'] === 'on') {
      if (empty($_POST["old_password"])) {
        $oldPasswordErr = "Old password is required";
      } else {
        $oldPassword = test_input($_POST["old_password"]);

        //convert the entered old password into hash in order to compare with the stored hash
        $hashedOldPassword = sha1($oldPassword);

        // Verify the entered password against the fetched password
        if ($hashedOldPassword !== $password) {
          $oldPasswordErr = "Old Password does not match";
          // Handle the error (e.g., prompt the user to try again)
        }
      }

      if (empty($_POST["new_password"])) {
        $newPasswordErr = "New password is required";
      } else {
        $newPassword = test_input($_POST["new_password"]);

        // Additional password checks (e.g., minimum length, etc.)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$_!%*?&]{8,}$/', $newPassword)) {
          $newPasswordErr = "Password must be at least 8 characters long, contain at least one lowercase and uppercase letter, one number, and one special character.";
        }
      }

      if (empty($_POST["confirm_password"])) {
        $confirmPasswordErr = "Confirm password is required";
      } else {
        $confirmPassword = test_input($_POST["confirm_password"]);

        // Check if confirm password matches new password
        if ($newPassword !== $confirmPassword) {
          $confirmPasswordErr = "Confirm password does not match new password";
        }
      }
    }

    if (empty($fileErr) && empty($firstNameErr) && empty($lastNameErr) && empty($ageErr) && empty($genderErr) && empty($phoneErr) && empty($professionErr) && empty($degreeErr) && empty($majorErr) && empty($instituteErr) && empty($addressErr) && empty($countryErr) && empty($stateErr) && empty($cityErr) && empty($commentErr) && empty($oldPasswordErr) && empty($newPasswordErr) && empty($confirmPasswordErr)) {
      $current_time = time();

      // Conditionally update the password if the checkbox is checked and validation passed
      if (isset($_POST['update_password']) && $_POST['update_password'] === 'on' && empty($oldPasswordErr) && empty($newPasswordErr) && empty($confirmPasswordErr)) {
        // Hash the new password
        $hashedNewPassword = sha1($newPassword);

        $current_time = time();

        // Update user data with hashed password
        $query = "UPDATE tbl_user SET user_password = '$hashedNewPassword', updated_on = '$current_time' WHERE user_id = '$user_id'";
        $conn->query($query);
      }

      // Update user profile data
      $update_profile = "UPDATE tbl_user_profile SET first_name = '$firstName', last_name = '$lastName', age = '$age', gender = '$gender', phone = '$phone', profession = '$profession', degree = '$degree', major = '$major', institute = '$institute', address = '$address', city = '$city', state = '$state', country = '$country', comments = '$comment', updated_on = '$current_time' WHERE user_id = '$user_id'";
      $conn->query($query);

      // Update profile photo if new file is uploaded
      if (isset($_POST['update_profilephoto']) && $_POST['update_profilephoto'] === 'on' && empty($fileErr)) {
        $destDir = 'photo/';

        // Check if the directory exists or create it
        if (!is_dir($destDir)) {
          mkdir($destDir, 0777, true);
        }

        // Determine the file extension based on MIME type
        if ($fileType === 'image/jpeg') {
          $fileExtension = '.jpeg';
        } elseif ($fileType === 'image/jpg') {
          $fileExtension = '.jpg';
        } elseif ($fileType === 'image/png') {
          $fileExtension = '.png';
        } else {
          $fileExtension = '';
        }
        $fileName = "user_" . $user_id . $fileExtension;
        $filePath = $destDir . $fileName;

        // If a profile photo with the same user ID exists, overwrite it
        if (file_exists($filePath)) {
          unlink($filePath); // Delete the existing file
        }

        move_uploaded_file($file['tmp_name'], $filePath);

        // Update profile photo filename in the database
        $query = "UPDATE tbl_user_profile SET file_name = '$fileName' WHERE user_id = '$user_id'";
        $conn->query($query);
      }

      if ($conn->query($update_profile) === TRUE) {
        $_SESSION['success'] = "User record updated successfully...";
        // Redirect to the results page
        header("Location: manage_user.php");
        exit(); // Ensure no further code is executed 
      } else {
        $error_message = "Error: " . $conn->error;
      }
    }
  }

  // Function to sanitize data
  function test_input($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  ?>

  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php include './layout/sidebar.php'; ?>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <?php include './layout/header.php'; ?>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col mb-4 order-0">
                <div class="d-flex align-items-end row">
                  <div class="col">
                    <div class="card mb-4 mx-auto">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Edit User Form</h3>
                      </div>
                      <div class="card-body">

                        <!-- Display error message if validation fails -->
                        <?php if (!empty($error_message)): ?>
                          <div class="alert alert-danger alert-dismissible" role="alert">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                        <?php endif; ?>

                        <!-- Display success message if file upload is successful -->
                        <?php if (!empty($success_message)): ?>
                          <div class="alert alert-success alert-dismissible" role="alert">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form id="dataForm" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                          <span>Please check the box first, if you want to update the profile photo...</span>
                          <div class="mb-3">
                            <input
                              type="checkbox"
                              class="form-check-input"
                              id="updateProfilePhoto"
                              name="update_profilephoto"
                              <?php if (isset($_POST['update_profilephoto']) && $_POST['update_profilephoto'] === 'on') echo "checked"; ?> />
                            <label class="form-check-label" for="updateProfilePhoto">Update Profile Photo</label>
                          </div>
                          <div class="mb-3">
                            <label for="profilePhoto" class="form-label fw-bold">Upload Profile Photo:</label>
                            <input type="file" class="form-control" id="profilePhoto" name="profilePhoto">
                            <span class="text-muted">Current Selected Profile Photo: <?php echo $profilePhoto; ?><br></span>
                            <span class=" error">
                              <?php
                              if (isset($fileErr) && !empty($fileErr)) {
                                echo $fileErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="firstName" class="form-label fw-bold">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?php echo $firstName; ?>" placeholder="Enter your first name" />
                            <span class="error">
                              <?php
                              if (isset($firstNameErr) && !empty($firstNameErr)) {
                                echo $firstNameErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="lastName" class="form-label fw-bold">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?php echo $lastName; ?>" placeholder="Enter your last name" />
                            <span class="error">
                              <?php
                              if (isset($lastNameErr) && !empty($lastNameErr)) {
                                echo $lastNameErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="age" class="form-label fw-bold">Age</label>
                            <input type="text" class="form-control" id="age" name="age" value="<?php echo $age; ?>" placeholder="Enter your age" />
                            <span class="error">
                              <?php
                              if (isset($ageErr) && !empty($ageErr)) {
                                echo $ageErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="Male" <?php if ($gender == "Male") echo "checked"; ?> />
                                <label class="form-check-label" for="male">Male</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="Female" <?php if ($gender == "Female") echo "checked"; ?> />
                                <label class="form-check-label" for="female">Female</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="other" value="Other" <?php if ($gender == "Other") echo "checked"; ?> />
                                <label class="form-check-label" for="other">Other</label>
                              </div>
                            </div>
                            <span class="error">
                              <?php
                              if (isset($genderErr) && !empty($genderErr)) {
                                echo $genderErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control" id="email" name="useremail" value="<?php echo $email; ?>" readonly>
                            <span class="error">
                              <?php
                              if (isset($emailErr) && !empty($emailErr)) {
                                echo $emailErr;
                              }
                              ?>
                            </span>
                          </div>
                          <span>Please check the box first, if you want to update the password...</span>
                          <div class="mb-3">
                            <input
                              type="checkbox"
                              class="form-check-input"
                              id="updatePassword"
                              name="update_password"
                              <?php if (isset($_POST['update_password']) && $_POST['update_password'] === 'on') echo "checked"; ?> />
                            <label class="form-check-label" for="updatePassword">Update Password</label>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-bold" for="old_password">Old Password</label>
                            <input
                              type="password"
                              id="old_password"
                              class="form-control"
                              name="old_password"
                              value="<?php if (isset($oldPassword) && !empty($oldPassword)) echo $oldPassword; ?>"
                              placeholder="Enter old password" />
                            <span class="error">
                              <?php
                              if (isset($oldPasswordErr) && !empty($oldPasswordErr)) {
                                echo $oldPasswordErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-bold" for="new_password">New Password</label>
                            <input
                              type="password"
                              id="new_password"
                              class="form-control"
                              name="new_password"
                              value="<?php if (isset($newPassword) && !empty($newPassword)) echo $newPassword; ?>"
                              placeholder="Enter new password" />
                            <span class="error">
                              <?php
                              if (isset($newPasswordErr) && !empty($newPasswordErr)) {
                                echo $newPasswordErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-bold" for="confirm_password">Confirm Password</label>
                            <input
                              type="password"
                              id="confirm_password"
                              class="form-control"
                              name="confirm_password"
                              value="<?php if (isset($confirmPassword) && !empty($confirmPassword)) echo $confirmPassword; ?>"
                              placeholder="Confirm new password" />
                            <span class="error">
                              <?php
                              if (isset($confirmPasswordErr) && !empty($confirmPasswordErr)) {
                                echo $confirmPasswordErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Enter your phone number">
                            <span class="error">
                              <?php
                              if (isset($phoneErr) && !empty($phoneErr)) {
                                echo $phoneErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="profession" class="form-label fw-bold">Profession</label>
                            <input type="text" class="form-control" id="profession" name="profession" value="<?php echo $profession; ?>" placeholder="Enter your profession (e.g., Software Engineer)">
                            <span class="error">
                              <?php
                              if (isset($professionErr) && !empty($professionErr)) {
                                echo $professionErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="degree" class="form-label fw-bold">Degree</label>
                            <input type="text" class="form-control" id="degree" name="degree" value="<?php echo $degree; ?>" placeholder="Enter your degree (e.g., Bachelor of Science)">
                            <span class="error">
                              <?php
                              if (isset($degreeErr) && !empty($degreeErr)) {
                                echo $degreeErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="major" class="form-label fw-bold">Major</label>
                            <input type="text" class="form-control" id="major" name="major" value="<?php echo $major; ?>" placeholder="Enter your major (e.g., Computer Science)">
                            <span class="error">
                              <?php
                              if (isset($majorErr) && !empty($majorErr)) {
                                echo $majorErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="institute" class="form-label fw-bold">Institute</label>
                            <input type="text" class="form-control" id="institute" name="institute" value="<?php echo $institute; ?>" placeholder="Enter your institute name">
                            <span class="error">
                              <?php
                              if (isset($instituteErr) && !empty($instituteErr)) {
                                echo $instituteErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" placeholder="Enter your address">
                            <span class="error">
                              <?php
                              if (isset($addressErr) && !empty($addressErr)) {
                                echo $addressErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="country" class="form-label fw-bold">Country</label>
                            <select class="form-select" id="country" name="country">
                              <option value="" selected>Select a Country</option>
                              <option value="Pakistan" <?php if ($country == "Pakistan") echo "selected"; ?>>Pakistan</option>
                              <option value="India" <?php if ($country == "India") echo "selected"; ?>>India</option>
                              <option value="United States of America" <?php if ($country == "United States of America") echo "selected"; ?>>United States of America</option>
                              <option value="United Kingdom" <?php if ($country == "United Kingdom") echo "selected"; ?>>United Kingdom</option>
                              <option value="Australia" <?php if ($country == "Australia") echo "selected"; ?>>Australia</option>
                            </select>
                            <span class="error">
                              <?php
                              if (isset($countryErr) && !empty($countryErr)) {
                                echo $countryErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3 ">
                            <label for="state" class="form-label fw-bold">State</label>
                            <!--loader-->
                            <div id="loader1" class="spinner-border spinner-border-sm text-primary hide" role="status">
                              <span class="visually-hidden">Loading...</span>
                            </div>
                            <select class="form-select" id="state" name="state">
                              <option value="" selected>Select a State</option>
                              <!-- Cities will be dynamically loaded here -->
                            </select>
                            <span class="error">
                              <?php
                              if (isset($stateErr) && !empty($stateErr)) {
                                echo $stateErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="city" class="form-label fw-bold">City</label>
                            <!--loader-->
                            <div id="loader2" class="spinner-border spinner-border-sm text-primary hide" role="status">
                              <span class="visually-hidden">Loading...</span>
                            </div>
                            <select class="form-select" id="city" name="city">
                              <option value="" selected>Select a City</option>
                              <!-- Cities will be dynamically loaded here -->
                            </select>
                            <span class="error">
                              <?php
                              if (isset($cityErr) && !empty($cityErr)) {
                                echo $cityErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="comments" class="form-label fw-bold">Comments</label>
                            <textarea class="form-control" id="comments" name="comment" rows="3" placeholder="Enter your comments"><?php echo $comment; ?></textarea>
                            <span class="error">
                              <?php
                              if (isset($commentErr) && !empty($commentErr)) {
                                echo $commentErr;
                              }
                              ?>
                            </span>
                          </div>
                          <button type="submit" name="submit" class="btn btn-primary">Save</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <?php include './layout/footer.php'; ?>
          <!-- / Footer -->

          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->

  <!-- build:js assets/vendor/js/core.js -->
  <script src="./public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="./public/assets/vendor/libs/popper/popper.js"></script>
  <script src="./public/assets/vendor/js/bootstrap.js"></script>
  <script src="./public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="./public/assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="./public/assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="./public/assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="./public/assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>

  <script>
    $(document).ready(function() {
      const data = {
        "Pakistan": {
          "Sindh": ["Karachi", "Hyderabad", "Sukkur"],
          "Punjab": ["Lahore", "Rawalpindi", "Multan"],
          "Khyber Pakhtunkhwa": ["Peshawar", "Mardan", "Abbottabad"],
          "Balochistan": ["Quetta", "Gwadar", "Turbat"],
        },
        "India": {
          "Gujarat": ["Ahmedabad", "Rajkot", "Jamnagar"],
          "Haryana": ["Faridabad", "Panipat", "Fatehabad"],
          "Uttar Pradesh": ["Aligarh", "Agra", "Lucknow"],
          "West Bengal": ["Kolkata", "Howrah", "Darjeeling"],
        },
        "United States of America": {
          "California": ["Los Angeles", "San Francisco", "San Diego"],
          "Texas": ["Houston", "Austin", "Dallas"],
          "New York": ["New York City", "Buffalo", "Albany"],
          "Florida": ["Miami", "Orlando", "Tampa"],
        },
        "United Kingdom": {
          "England": ["London", "Manchester", "Liverpool"],
          "Scotland": ["Edinburgh", "Glasgow", "Aberdeen"],
          "Wales": ["Cardiff", "Swansea", "Newport"],
          "Northern Ireland": ["Belfast", "Londonderry", "Lisburn"],
        },
        "Australia": {
          "New South Wales": ["Sydney", "Newcastle", "Wollongong"],
          "Victoria": ["Melbourne", "Geelong", "Ballarat"],
          "Queensland": ["Brisbane", "Gold Coast", "Townsville"],
          "Western Australia": ["Perth", "Fremantle", "Bunbury"]
        },
      };

      const country = "<?php echo $country; ?>";
      const state = "<?php echo $state; ?>";
      const city = "<?php echo $city; ?>";

      if (country) {
        $.each(data[country], function(cstate) {
          $('#state').append(`<option value="${cstate}" ${state === cstate ? 'selected' : ''}>${cstate}</option>`);
        });
        if (state) {
          $.each(data[country][state], function(index, ccity) {
            $('#city').append(`<option value="${ccity}" ${city === ccity ? 'selected' : ''}>${ccity}</option>`);
          });
        }
      }
      $('#country').change(function() {
        const Country = $(this).val();
        $('#state').empty().append('<option value="">Select a State</option>');

        if (Country) {
          $('#loader1').css('display', 'inline-block');
          setTimeout(() => {
            $.each(data[Country], function(state) {
              $('#state').append(`<option value="${state}">${state}</option>`);
            });
            $('#loader1').css('display', 'none');
          }, 500);
        }
      });

      $('#state').change(function() {
        const State = $(this).val();
        const Country = $('#country').val();
        $('#city').empty().append('<option value="">Select a City</option>');

        if (State) {
          $('#loader2').css('display', 'inline-block');
          setTimeout(() => {
            $.each(data[Country][State], function(index, city) {
              $('#city').append(`<option value="${city}">${city}</option>`);
            });
            $('#loader2').css('display', 'none');
          }, 500);
        }
      });
    });
  </script>
</body>

</html>