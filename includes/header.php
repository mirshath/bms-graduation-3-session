<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- -------------------  -->
    <!-- fonts  -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
    <!-- -------------------  -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.2/qrcode.min.js"></script>

    <style>
        .eb-garamond {
            font-family: "EB Garamond", serif;
            font-optical-sizing: auto;

        }

        .btn-primary {
            /* background: #c9daf8; */
        }

        .wrap-input-1 .input:focus {
            outline: none;
        }

        .wrap-input-1 {
            width: 100%;
            /* margin: 40px 3%; */
            position: relative;
        }

        .wrap-input-1 .input {
            border: 0;
            padding: 7px 10px;
            border-bottom: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
            letter-spacing: 1px;
            background-color:
                #f5f6fd;
        }

        .wrap-input-1 .input~.focus-border {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color:
                brown !important;
            /* background-color:
                #351c75; */
            transition: 0.4s;
        }

        .wrap-input-1 .input:focus~.focus-border {
            width: 100%;
            transition: 0.4s;
        }

        .form-label {
            font-weight: 700;
            font-family: "EB Garamond", serif;
            font-size: 18px;

        }

        .lft_border {
            border-left: 2px solid brown !important;
        }



        /* -----------------------  */
        /* pre loading  */
        /* -----------------------  */

        /* Preloader styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            /* or any background color */
            z-index: 9999;
            /* Ensure it covers the entire page */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Optional: Style the GIF if needed */
        .preloader-gif {
            width: 150px;
            /* Adjust size as necessary */
            /* height: 100px; */
            /* Adjust size as necessary */
        }
    </style>
</head>

<!-- Preloader -->
<div id="preloader">
    <img src="./images/animation.gif" alt="Loading..." class="preloader-gif">
</div>

<script>
    window.addEventListener("load", function() {
        document.getElementById("preloader").style.display = "none";
    });
</script>


<style>
    .btn-primary {
        background: rgb(99, 34, 25);
        background: radial-gradient(circle, rgba(99, 34, 25, 1) 0%, rgba(70, 20, 12, 1) 95%);
        border: none !important;
    }

    /* Hide the default radio button */
    .custom-radio {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        position: relative;
        width: 18px;
        height: 18px;
        border: 2px solid brown;
        border-radius: 50%;
        background-color: #fff;
        cursor: pointer;
        margin-right: 10px;
        outline: none;
        vertical-align: middle;
    }

    /* Create the checked state */
    .custom-radio:checked {
        border-color: brown;
        background-color: brown;
    }

    /* Add a custom checkmark (the inner circle) */
    .custom-radio:checked::after {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        /* background-color: white; */
        border-radius: 50%;
        top: 4px;
        left: 4px;
    }

    /* Hover effect (optional) */
    .custom-radio:hover {
        border-color: brown;
    }
</style>

<!-- <body style="background-color:#f3f3f3;"> -->

<body style="background-image: url('./images/back.jpg'); 
             background-size: cover; 
             background-attachment: fixed; 
             background-position: center; 
             background-repeat: no-repeat;">