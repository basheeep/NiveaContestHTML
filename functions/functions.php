<?php


//************* Some helper function  *********/

// for redirecting page
function Redirect_to($location)
{
    header("Location:{$location}");
    exit;
}
// Display Messages through sessions
function errorMsg()
{
    if (isset($_SESSION['error'])) {
        $Output = "<div class='alert alert-danger alert-dismissible fade show '>";
        $Output .= htmlentities($_SESSION['error']);
        $Output .= "<button type='button'class='close' data-dismiss='alert' aria-label='Close'>";
        $Output .= "<span aria-hidden='true'>&times;</span>";
        $Output .= '</div>';

        echo $Output;
        unset($_SESSION['error']);
    }
}
function successMsg()
{
    if (isset($_SESSION['success'])) {
        $Output = "<div class='alert alert-success alert-dismissible fade show '>";
        $Output .= htmlentities($_SESSION['success']);
        $Output .= "<button type='button'class='close' data-dismiss='alert' aria-label='Close'>";
        $Output .= "<span aria-hidden='true'>&times;</span>";
        $Output .= '</div>';

        echo $Output;
        unset($_SESSION['success']);
    }
}

// validation errors in form
// we can alao use delimitter so that we dont have to worry anout .= things :) means we can use html code


function validation_errors($error_message)
{
    $error_message = <<<DELIMITER

<div class="alert alert-danger alert-dismissible " role="alert">
     <strong> WARNING:!</strong> $error_message
     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
     <span aria-hidden="true">&times;</span>
     </button>
</div>
DELIMITER;
    return $error_message;
}
// check if emqil is already existed
function email_Exist($email)
{
    $sql = " SELECT * FROM users WHERE u_email = '$email' ";
    $result = query($sql);
    confirm($result);
    if (row_count($result) > 0) {
        return true;
    } else {
        return false;
    }
}




// user  registeration
$register_errors = [];
function register_user()
{
 global $fName , $lName,  $email , $phone , $password , $confirmPassword;
 global $register_errors ;

    if (isset($_POST['register'])) {

        $fName = escape($_POST['f_name']);
        $fName = trim($fName);
        $lName = escape($_POST['l_name']);
        $lName = trim($lName);
        $phone = escape($_POST['phone']);
        $phone = trim($phone);
        $email = escape($_POST['email']);



        $password = escape($_POST['password']);
        $confirmPassword = escape($_POST['c_password']);


        if (strlen($fName) < 3) {
           $register_errors['fname'] = '*First Name minimum length is 3  ';
        }
        if (strlen($lName) < 3) {
          $register_errors['lname'] = '*Last Name minimum length is 3  ';
       }
        if (empty($email)) {
          $register_errors['mail'] = '*Email field required';
        }

        if (email_Exist($email)) {
          $register_errors['mail'] = 'Email alredy existed';
        }

        if (!is_numeric($phone)) {
          $register_errors['phone'] = 'phone field required only numbers';
        }

        if (strlen($password) < 6) {
          $register_errors['pass'] = 'password minimum length is 6';
        }

        if (empty($confirmPassword)) {
          $register_errors['pass2'] = 'Repeat password required';
        }

        if ($password !== $confirmPassword) {
          $register_errors['pass2'] = 'password confirm filed not macthed';
        }

        if (empty($register_errors)) {
          $sql = "INSERT INTO users(u_fname,u_lname,u_phone,u_email,u_password) VALUES('$fName', '$lName', '$phone', '$email', '$password')";
          $execute = query($sql);
          confirm($execute);
          if ($execute) {
              $_SESSION['success'] = 'your are registered successfully.';
              Redirect_to('login.php');
        }

            }

    }
}




// user login
$login_errors = [];
function user_login()

{
  global $login_errors;
    if (isset($_POST['login'])) {
        $email = escape($_POST['email']);
        $password = escape($_POST['password']);
        $errors = [];
        if (empty($email)) {
            $errors[] = 'Email field is required';
        }
        if (empty($password)) {
            $errors[] = 'Password field is required';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo validation_errors($error);
            }
        } else {
            $sql = " SELECT * FROM users WHERE u_email = '$email' AND u_password = '$password' ";
            $execute = query($sql);

            if (row_count($execute) == 1) {
                $row = fetch_array($execute);
                $_SESSION['userId'] = $row['u_id'];
                $_SESSION['userFName'] = $row['u_fname'];
                $_SESSION['userLLast'] = $row['u_lname'];
                $_SESSION['userEmail'] = $row['u_email'];
                $_SESSION['userPhone'] = $row['u_phone'];


                Redirect_to('index.php');
            } else {
              $login_errors['u'] = 'Invalid Username or Password';

            }
        }
    }
}

function login_user()
{
    if (isset($_SESSION['userId'])) {
        return true;
    }
}
// restriction on pages only logeg in user allowed to visit page

function confirm_log_in_user()
{
    if (!login_user()) {
        $_SESSION['error'] = 'Login first! please';
        Redirect_to('../login.php');
    }
}



function add_contest() {

  $errors = [];



     if (isset($_POST['add-con'])) {

         $Ic = escape($_POST['ic_num']);

         $gender= escape($_POST['gender']);

         $occupation = escape($_POST['occupation']);


          // For image feature

        $image_name = $_FILES['image']['name']; // file name
        $image_tmp_name = $_FILES['image']['tmp_name']; // file temp name in srver
        $image_size = $_FILES['image']['size']; // file size

        $image_ext = explode('.', $image_name); // we get two parts here first name and second extention

        $image_actual_ext = strtolower(end($image_ext)); // yaha pr hm second part len gy array se end function se.. extenion.

        $allowed_files = ['jpg', 'jpeg', 'png'];



         if (strlen($Ic) > 12) {
          $errors[] = ' Maximum Length is 12   ';
         }

         if ($gender == "gender") {
          $errors[] = ' Please Select a Gender   ';
       }

       if ($occupation== "occupation") {
        $errors[] = 'Please Select a occupation   ';
     }


         if (!in_array($image_actual_ext, $allowed_files)) {
          $errors[] = 'Only jpg and png file can be upload';
      } else {
          $image_new_name = uniqid('', true) . '.' . $image_actual_ext; // we need to genertae nee name for image. otherwise it will replace previoue if same name image
      }

       if (!empty($errors)) {

        foreach ($errors as $error) {
          echo validation_errors($error);
        }

       }else {
        $sql = "INSERT INTO contests(c_num,c_gender,c_occ,c_img) VALUES('$Ic', '$gender', '$occupation', '$image_new_name')";
        $execute = query($sql);
        confirm($execute);
        if ($execute) {
            move_uploaded_file($image_tmp_name, "image/$image_new_name");
            $_SESSION['success'] = 'submitted successfully.';
            Redirect_to('contest.php');
      }
       }




     }


}



?>
