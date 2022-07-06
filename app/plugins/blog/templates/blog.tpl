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
        <!-- Google Analytics -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-91106209-1', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- RSS -->
        <link rel="alternate" href="{ROOT_URL}blog/rss" title="{TITLE} Feed" type="application/rss+xml" />
        <!-- Custom styles for this template -->
        {TEMPLATE_CSS_INCLUDES}
        <!-- Custom javascript for this template -->
        {TEMPLATE_JS_INCLUDES}
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!--Brand and toggle should get grouped for better mobile display -->
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
                        <ul class="nav navbar-nav">
                            {RECENT_MESSAGES}
                            {STATUS}
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="foot-pad">
                {ISSUES}
                <div class="row">
                    <div class="container">
                        {ERROR}
                        {NOTICE}
                        {SUCCESS}
                    </div>
                </div>
                {/ISSUES}
                <div class="row">
                    <div class="container">
                        <div class="page-header">
                            <h1 class="blog-title">{SITENAME} Blog</h1>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 blog-main">
                                {CONTENT}
                            </div>
                            <!-- /.blog-main -->
                            <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
                                <div class="sidebar-module">
                                    {SIDEBAR}
                                </div>
                                <div class="sidebar-module">
                                    {SIDEBAR2}
                                </div>
                            </div>
                            <!-- /.blog-sidebar -->
                        </div>
                        <!-- /.row -->
                    </div>
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