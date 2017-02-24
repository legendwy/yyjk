<html>
<body>
{{--------------------------------------------------}}
{{--<div style="background-color: #85cc6e"> 346</div>--}}
{{--<script>--}}
{{--window.addEventListener('message', function (e) {--}}
{{--if (e.source != window.frames[0]) return;--}}
{{--window.frames[0].postMessage("{{$token}}", 'http://shl17.huiyaoba.com');--}}
{{--}, false);--}}
{{--</script>--}}
{{--<iframe style="background-color: #cc2349" frameborder=0 width=1000 height=800 scrolling=no src="http://shl17.huiyaoba.com/Home/index/login"></iframe>--}}
<script>
    window.addEventListener('message', function (e) {
        var data = {
            'token':'{{ $data['token'] }}',
            'url':'{{ $data['url'] }}'
        }
        if (e.source != window.frames[0]) return;
        window.frames[0].postMessage(data, 'http://smyb05.huiyaoba.com');
    }, false);
</script>
<iframe style="display: none" frameborder=0 scrolling=no width="100%"
        src="http://smyb05.huiyaoba.com"></iframe>

{{--------------------------------------------------}}

</body>
</html>
