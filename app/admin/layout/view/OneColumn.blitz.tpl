<!DOCTYPE html>
<html lang="pl">
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
        
        
        
        <link rel="stylesheet" href="/admin/assets/css/font-icons/entypo/css/entypo.css">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
        
        <link rel="stylesheet" href="/admin/assets/css/neon-core.css">
        <link rel="stylesheet" href="/admin/assets/css/neon-theme.css">
        <link rel="stylesheet" href="/admin/assets/css/neon-forms.css">
        <link rel="stylesheet" href="/admin/assets/css/custom.css">
        
        
        <script src="/admin/assets/js/gsap/main-gsap.js"></script>
        <script src="/admin/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
        <script src="/admin/assets/js/bootstrap.js"></script>
        <script src="/admin/assets/js/joinable.js"></script>
        <script src="/admin/assets/js/resizeable.js"></script>
        <script src="/admin/assets/js/neon-api.js"></script>
        <script src="/admin/assets/js/jquery.validate.min.js"></script>
        


        <!-- JavaScripts initializations and stuff -->
        <script src="/admin/assets/js/neon-custom.js"></script>


        <!-- Demo Settings -->
        <script src="/admin/assets/js/neon-demo.js"></script>

    </head>
    <body class="page-body login-page login-form-fall" data-url="{{this::baseUrl()}}">
        <!-- This is needed when you send requests via Ajax -->
        <script type="text/javascript">
            var baseurl = '{{this::baseUrl()}}/';
        </script>
        [placeholder:main]
        
        {{this::renderUserJs()}}
    </body>
</html>