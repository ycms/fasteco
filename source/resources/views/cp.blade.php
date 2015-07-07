<html>
<head>
    <title>my I frame is as tall as your page</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
    <style>
        body,html {padding:0;margin: 0;}
    </style>
</head>
<body style="margin:0;">
<iframe style="height:100%;width:100%;margin: 0px; padding:0px;" width="100%" height="100%" frameborder="0" id="iframe1" src="/wp-admin" style="width:100%;" @if(0)onload="this.height=$(window).height();"@endif></iframe>
</body>
</html>