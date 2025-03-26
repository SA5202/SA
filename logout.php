<?php
session_start();
session_unset();
session_destroy();

// 刷新父頁面
echo '<script>
        parent.location.reload();
      </script>';
exit();
