</div>

<script src="/resource/js/jquery-3.2.1.min.js"></script>
<script src="/resource/js/bootstrap.js"></script>
<?php
if (isset($rScript)) {
    echo scriptForLayout($rScript);
    unset($rScript);
}
?>
</body>
</html>