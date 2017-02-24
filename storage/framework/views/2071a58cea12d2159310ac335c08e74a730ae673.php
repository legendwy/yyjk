<html>
<body>









<script>
    window.addEventListener('message', function (e) {
        var data = {
            'token':'<?php echo e($data['token']); ?>',
            'url':'<?php echo e($data['url']); ?>'
        }
        if (e.source != window.frames[0]) return;
        window.frames[0].postMessage(data, 'http://smyb05.huiyaoba.com');
    }, false);
</script>
<iframe style="display: none" frameborder=0 scrolling=no width="100%"
        src="http://smyb05.huiyaoba.com"></iframe>



</body>
</html>
