<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BMS DEGREE CONVOCATION 2026</title>

    <link rel="stylesheet" href="assets/style.css">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">



    <!-- ------------------------  -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- ------------------------  -->

    <!-- Include CKEditor -->
    <script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>



    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- new   -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css" />





    <style>
        /* common CSS  */
        /* ----------------------------------------------  */


        .form-control {
            display: block;
            width: 100%;
            padding: 0.3rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--bs-body-color);
            background-color: var(--bs-body-bg);
            background-clip: padding-box;
            border: var(--bs-border-width) solid var(--bs-border-color);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: var(--bs-border-radius);
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-control:focus {
            box-shadow: 0 0 5px #c110104f;
            border-color: #c110104f;
        }

        .cke_notification_warning {
            background: #c83939;
            border: 1px solid #902b2b;
            display: none;
        }


        /* ----------------------------------------------------------  */

        /* this is for student registration  */

        .form-label {
            margin-bottom: 0.7rem;
            color: #878180;
        }

        .form-check-label {
            color: #878180;
        }

        .btn-primary {
            /* background: rgb(134, 55, 43); */
            /* background: radial-gradient(circle,
                    rgba(134, 55, 43, 1) 0%,
                    rgba(92, 29, 19, 1) 100%); */

            background: linear-gradient(90deg, rgb(151, 30, 11) 0%, rgb(128, 20, 4) 100%);

            border: 1px solid white;
        }

        .btn-primary:hover {
            background-color: #c83939;
        }
    </style>
</head>

<body id="page-top">



    <script>
        // function checkSession() {
        //     fetch('check_session.php')
        //         .then(response => response.json())
        //         .then(data => {
        //             if (!data.valid) {
        //                 // Redirect instantly if session is invalid or Unknown Admin
        //                 window.location.href = 'login';
        //             }
        //         })
        //         .catch(error => console.error('Session check failed:', error));
        // }

        // // Check every 5 seconds (5000 ms)
        // // setInterval(checkSession, 5000);
        //     $(document).ready(function() {
        //     checkSession(); // immediate check
        //     setInterval(checkSession, 3000); // repeat every 3 seconds
        // });
    </script>




    <script>
        function checkSession() {
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        // Redirect if session invalid or unknown
                        window.location.href = 'login';
                    }
                })
                .catch(error => console.error('Session check failed:', error));
        }

        $(document).ready(function() {
            checkSession(); // Immediate check on load
            setInterval(checkSession, 3000); // Recheck every 3 seconds
        });
    </script>