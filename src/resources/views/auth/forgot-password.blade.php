<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定 - AITECH</title>
    <link rel="stylesheet" href="{{ asset('css/auth/forgot-password.css') }}">
</head>

<body>
    <!-- 背景アニメーション -->
    <div class="background">
    </div>

    <!-- メイン -->
    <div class="container">
        <div class="login-card">

            <!-- ロゴ -->
            <div class="logo">
                <span class="logo-text"></span>
            </div>

            <!-- タイトル -->
            <h2 class="password-title">パスワード再設定</h2>

            <p class="password-description">
                登録されているメールアドレスを入力してください。<br>
                パスワード再設定用のメールを送信します。
            </p>

            {{-- 案内メッセージ --}}
            @if (session('status'))
            <div class="success-message" style="display:flex; align-items:center;">
                <svg class="success-icon" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" style="width:20px; height:20px; margin-right:6px;">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <!-- バリデーションメッセージ -->
            @if ($errors->any())
            <div class=" message-error" style="color: red;">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <!-- 再設定メール送信用フォーム -->
            <form method="POST" action="{{ route('password.email') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <input
                        type="email"
                        name="email"
                        class="form-input"
                        placeholder="メールアドレス"
                        required>
                </div>

                <button type="submit" class="login-button">
                    送信
                </button>

                <div class="forgot-password">
                    <a href="{{ route('auth.login.form') }}">ログインページに戻る</a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>