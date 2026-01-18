<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定 - AITECH</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>
    <!-- 背景アニメーション（ログイン画面と同じ） -->
    <div class="background">
    </div>

    <!-- メインコンテンツ（カードUI） -->
    <div class="container">
        <div class="login-card">

            <!-- ロゴ -->
            <div class="logo">
                <span class="logo-text"></span>
            </div>

            <h2 class="reset-title">パスワード再設定</h2>

            <!-- 再設定フォーム -->
            <form class="login-form" method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- hidden: トークン -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- hidden: メールアドレス（URL/query から取得） -->
                <input type="hidden" name="email" value="{{ request()->email }}">

                <!-- パスワード -->
                <div class="form-group">
                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="新しいパスワード"
                        required>
                </div>

                <!-- パスワード確認 -->
                <div class="form-group">
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="新しいパスワード（確認）"
                        required>
                </div>

                <button type="submit" class="login-button">
                    パスワードを設定
                </button>

            </form>
        </div>
    </div>

</body>

</html>