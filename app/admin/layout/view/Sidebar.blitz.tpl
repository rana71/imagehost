<!DOCTYPE html>
<html lang="en">
    <head>
        <base href='{{this::baseUrl()}}/' />
        <meta charset="UTF-8" />
        <meta name="robots" content="noindex, nofollow, noarchive" />
        <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet" />
        <title>{{$title}}</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        
        
        
        
        <link rel="stylesheet" href="/admin/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
        <link rel="stylesheet" href="/admin/assets/css/font-icons/entypo/css/entypo.css">
        <link rel="stylesheet" href="/admin/assets/css/font-icons/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
        <link rel="stylesheet" href="/admin/assets/css/bootstrap.css">
        <link rel="stylesheet" href="/admin/assets/css/neon-core.css">
        <link rel="stylesheet" href="/admin/assets/css/neon-theme.css">
        <link rel="stylesheet" href="/admin/assets/css/neon-forms.css">
        <link rel="stylesheet" href="/admin/assets/css/custom.css">
        <link rel="stylesheet" href="/admin/css/styles.css">

        <script>
//            $.noConflict();
                    </script>

        <!--[if lt IE 9]><script src="/admin/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="page-body  page-left-in" data-url="{{this::baseUrl()}}">
        <!-- This is needed when you send requests via Ajax -->
        <script type="text/javascript">
            var baseurl = '{{this::baseUrl()}}/';
        </script>

        <div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
            <div class="sidebar-menu">

                <div class="sidebar-menu-inner">
                    
                    <header class="logo-env">

                        <!-- logo collapse icon -->
                        <div class="sidebar-collapse">
                            <a href="#" class="sidebar-collapse-icon with-animation"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                                <i class="entypo-menu"></i>
                            </a>
                        </div>


                        <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
                        <div class="sidebar-mobile-menu visible-xs">
                            <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                                <i class="entypo-menu"></i>
                            </a>
                        </div>

                    </header>

                    
                    [placeholder:left]
                    
                </div>

            </div>
            
            <div class="main-content">
                [placeholder:right]
            </div>

        </div>
        {{this::renderUserJs()}}
        <!-- Imported styles on this page -->
        <link rel="stylesheet" href="/admin/assets/js/rickshaw/rickshaw.min.css">

    </body>
</html>