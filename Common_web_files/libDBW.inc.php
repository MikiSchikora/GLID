<?php

function headerDBW($title) {
    return "<html lang=\"en\">
<head>
<meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<!--title>$title</title-->
       <!-- Bootstrap styles -->
    <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\" integrity=\"sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u\" crossorigin=\"anonymous\">
  
    <!-- IE 8 Support-->
    <!--[if lt IE 9]>
      <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>
      <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>
    <![endif]--> 
        <link rel=\"stylesheet\" href=\"DataTable/jquery.dataTables.min.css\"/>
        <script type=\"text/javascript\" src=\"DataTable/jquery-2.2.0.min.js\"></script>
        <script type=\"text/javascript\" src=\"DataTable/jquery.dataTables.min.js\"></script>
        
    <!-- Extra styles-->
    <style>
        .check_good {display: inline;}
        .outline {
            outline-color: black;
            outline-style: double;
        }
    </style>
    
       

</head>
<body bgcolor=\"#ffffff\">
<!--div class= \"container\"-->
<!--h1>DBW - $title</h1-->
    
<nav class=\"navbar navbar-inverse\">
      <div class=\"container\">
        <div class=\"navbar-header\">
          <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\" aria-expanded=\"false\" aria-controls=\"navbar\">
            <span class=\"sr-only\">Toggle navigation</span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
          </button>
          <a class=\"navbar-brand\" href=\"index.php\">GLID project</a>
        </div>
        <div id=\"navbar\" class=\"collapse navbar-collapse\">
          <ul class=\"nav navbar-nav\">
            <li><a href=\"index.php\">Home</a></li>
            <li><a href=\"help.html\">Help</a></li>
            <li><a href=\"contact.html\">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class=\"container\">

      <h1 class=\"text-center\">GLID: Gene to Literature Integrative Database</h1>
      
      
";
}

function footerDBW() {
    return '
        <!--/div-->
</body>
</html>';
}

function errorPage($title, $text) {
    return headerDBW($title) . $text . footerDBW();
}
