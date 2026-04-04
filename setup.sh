#!/data/data/com.termux/files/usr/bin/bash
# ============================================================
# setup.sh — Termux環境セットアップスクリプト
# 使い方: bash setup.sh
# ============================================================

set -e

echo "=================================="
echo " Portfolio Blog セットアップ"
echo "=================================="

# 必要パッケージのインストール
echo ""
echo "[1/4] 必要パッケージをインストールしています..."
pkg update -y
pkg install -y php php-sqlite sqlite

# dataディレクトリの作成とパーミッション設定
echo ""
echo "[2/4] ディレクトリを準備しています..."

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
DATA_DIR="$SCRIPT_DIR/data"

mkdir -p "$DATA_DIR"
chmod 750 "$DATA_DIR"

echo "  データディレクトリ: $DATA_DIR"

# 初期パスワードの案内
echo ""
echo "[3/4] セキュリティ設定の確認..."
echo "  ⚠️  初期ログイン情報:"
echo "     ユーザー名: admin"
echo "     パスワード:  changeme123"
echo ""
echo "  ⚠️  【重要】必ず最初にパスワードを変更してください！"
echo "     変更方法: php admin/change_password.php"

# PHP内蔵サーバーの起動
echo ""
echo "[4/4] PHPサーバーを起動します..."
echo "  アクセス: http://localhost:8080"
echo "  管理画面: http://localhost:8080/admin/login.php"
echo "  停止:     Ctrl+C"
echo ""
echo "=================================="

cd "$SCRIPT_DIR"
php -S 0.0.0.0:8080 -t .
