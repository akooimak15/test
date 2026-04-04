<?php
// ============================================================
// admin/logout.php — ログアウト処理
// ============================================================

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

logout();
header('Location: /admin/login.php?loggedout=1');
exit;
