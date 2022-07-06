<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{TITLE}</title>
        <meta name="description" content="{PAGE_DESCRIPTION}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {AUTHOR}
        {ROBOT}
        <link rel="alternate" hreflang="en-us" href="alternateURL">
        <link rel="icon" href="{ROOT_URL}images/favicon.ico">
        <!-- Required CSS -->
        <link rel="stylesheet" href="{FONT_AWESOME_URL}font-awesome.min.css">
        <link rel="stylesheet" href="{BOOTSTRAP_CDN}css/bootstrap.min.css" crossorigin="anonymous">
        <!-- Google Analytics -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-91106209-1', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- Custom styles for this template -->
        {TEMPLATE_CSS_INCLUDES}
        <!-- Custom javascript for this template -->
        {TEMPLATE_JS_INCLUDES}
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!--Brand and toggle should get grouped for better mobile display
                but I had to account for additional menus-->
            <div class="navbar-header">
                <a class="navbar-brand" href="{ROOT_URL}">{SITENAME}</a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse" style="">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="container">
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    {MAINNAV}
                    <div class="navbar-right">
                        <ul class="nav navbar-nav" style="">
                            {RECENT_MESSAGES}
                            {STATUS}
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container-fluid toppad foot-pad">
            {ISSUES}
            <div class="row">
                <div class="container">
                    {ERROR}
                    {NOTICE}
                    {SUCCESS}
                    {INFO}
                </div>
            </div>
            {/ISSUES}
            <div class="row">
                <div class="container">
                    {CONTENT}
                </div>
            </div>
            <div class="row">
                <footer>
                    {FOOT}
                    {COPY}
                </footer>
            </div>
        </div>
        <!-- Bootstrap core JavaScript and jquery -->
        <script src="{BOOTSTRAP_CDN}js/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="{JQUERY_CDN}jquery.min.js" crossorigin="anonymous"></script>
    </body>
</html>