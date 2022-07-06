<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{TITLE}</title>
        <meta name="description" content="{PAGE_DESCRIPTION}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="The Tempus Project">
        {ROBOT}
        <link rel="alternate" hreflang="en-us" href="alternateURL">
        <link rel="icon" href="{ROOT_URL}Images/favicon.ico">
        <!-- Required CSS -->
        <link rel="stylesheet" href="{FONT_AWESOME_URL}font-awesome.min.css">
        <link rel="stylesheet" href="{BOOTSTRAP_CDN}css/bootstrap.min.css" crossorigin="anonymous">
        <!-- Custom styles for this template -->
        {TEMPLATE_CSS_INCLUDES}
        <!-- Custom javascript for this template -->
        {TEMPLATE_JS_INCLUDES}
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="{ROOT_URL}">{SITENAME}</a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- Top Menu Items -->
            <div class="container">
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    {MAINNAV}
                    <div class="navbar-right">
                        <ul class="nav navbar-nav">
                            {RECENT_MESSAGES}
                            {STATUS}
                        </ul>
                    </div>
                </div>
            </div>
            {ADMIN}
                {ADMINNAV}
            {/ADMIN}
        </nav>
        <div class="container-fluid">
            {ISSUES}
            <div class="row">
            <div class="container">
                <div class="col-xlg-11 col-xlg-offset-1 col-lg-10 col-lg-offset-2 col-med-offset-2 col-md-10 col-sm-offset-3 col-sm-9 col-xs-offset-3 col-xs-9 row">
                    {ERROR}
                    {NOTICE}
                    {SUCCESS}
                </div>
            </div>
            </div>
            {/ISSUES}
            <div class="row">
                <div class="col-xlg-11 col-xlg-offset-1 col-lg-10 col-lg-offset-2 col-med-offset-2 col-md-10 col-sm-offset-3 col-sm-9 col-xs-offset-3 col-xs-9 main">
                    {CONTENT}
                </div>
            </div>
            <div class="row">
                <footer>
                    <div class="sticky-foot">
                        <div class="sticky-foot-head" id=""></div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center sticky-copy">
                            <p class="text-muted">Powered by <a href="https://www.thetempusproject.com">The Tempus Project</a>.</p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <!-- Bootstrap core JavaScript and jquery -->
        <script src="{BOOTSTRAP_CDN}js/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="{JQUERY_CDN}jquery.min.js" crossorigin="anonymous"></script>
    </body>
</html>