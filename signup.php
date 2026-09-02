<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="About the site"/>
    <meta name="author" content="Author name"/>
    <title>THE TITLE</title>

    <!-- core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="css/font-awesome.min.css" rel="stylesheet"/>
    <link href="css/animate.min.css" rel="stylesheet"/>
    <link href="css/main.css" rel="stylesheet"/>
</head>

<body>

    <!-- Menu bar -->
    <section id="header" style="background-color: #7b9da6;">
        <div class="row">

            <div class="col-md-2"
                 style="font-size: 30px; color:#fcfeff;">
                Data Explorer
            </div>

            <div class="col-md-10" style="text-align: right;">
                <a href="#" style="color: #fcfeff;">Home</a>

                <a href="#"
                   style="margin-left: 20px; color: #fcfeff;">
                    country info
                </a>

                <a href="#"
                   style="margin-left: 20px; color: #fcfeff;">
                    year
                </a>
            </div>

        </div>
    </section>


    <!-- Sign Up section -->
    <section id="section1">

        <div class="title" style="color: #7b9da6;">
            SIGN UP
        </div>

        <form action="register.php"
              class="form_design"
              method="post">

            <span style="color: #7b9da6;">
                Username:
            </span>

            <input type="text" name="fname">
            <br/>

            <span style="color: #7b9da6;">
                Password:
            </span>

            <input type="password" name="pass">
            <br/><br/>

            <input type="submit"
                   value="Sign Up"
                   style="background-color: #7b9da6;
                          color: #fcfeff;">

            <br/><br/>

            <span style="color: #7b9da6;">
                Already a user?
            </span>

            <a href="index.php"
               style="color: #7b9da6;">
                Sign In
            </a>

        </form>

    </section>


    <!-- Footer -->
    <section id="footer"
             style="background-color: #7b9da6;">
    </section>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/wow.min.js"></script>

</body>
</html>