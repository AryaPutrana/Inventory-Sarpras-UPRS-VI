<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - SI Inventory Sarpras UPRS VI</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        /* =========================================================
           RESET
        ========================================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;

            /*
             * Tidak boleh scroll
             */
            overflow: hidden;

            /*
             * Background utama
             */
            background:
                radial-gradient(
                    circle at 4% 8%,
                    rgba(255, 255, 255, 0.09) 0,
                    rgba(255, 255, 255, 0.09) 170px,
                    transparent 171px
                ),
                radial-gradient(
                    circle at 98% 95%,
                    rgba(255, 255, 255, 0.07) 0,
                    rgba(255, 255, 255, 0.07) 260px,
                    transparent 261px
                ),
                linear-gradient(
                    135deg,
                    #173b91 0%,
                    #2455c3 50%,
                    #2d68dc 100%
                );

            position: relative;
        }


        /* =========================================================
           HEADER LOGO
        ========================================================= */

        .logo-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;

            width: 100%;
            height: 135px;

            padding: 18px 55px;

            background: #ffffff;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border-bottom: 4px solid #1d4ed8;

            box-shadow:
                0 4px 18px rgba(0, 0, 0, 0.15);

            z-index: 100;
        }


        /* =========================================================
           GROUP LOGO KIRI
        ========================================================= */

        .logo-left-group {
            display: flex;
            align-items: center;
            gap: 28px;
        }


        /* =========================================================
           LOGO KANAN
        ========================================================= */

        .logo-right {
            display: flex;
            align-items: center;
            justify-content: center;
        }


        /* =========================================================
           SEMUA LOGO
        ========================================================= */

        .logo-jaya-raya,
        .logo-dprkp,
        .logo-uprs {
            display: block;

            width: auto;

            object-fit: contain;

            background: transparent;

            transition: transform 0.2s ease;
        }


        /* Logo Jaya Raya */

        .logo-jaya-raya {
            height: 78px;
            max-width: 175px;
        }


        /* Logo DPRKP */

        .logo-dprkp {
            height: 78px;
            max-width: 175px;
        }


        /* Logo UPRS VI */

        .logo-uprs {
            height: 88px;
            max-width: 205px;
        }


        /* Hover logo */

        .logo-jaya-raya:hover,
        .logo-dprkp:hover,
        .logo-uprs:hover {
            transform: scale(1.04);
        }


        /* =========================================================
           AREA LOGIN
        ========================================================= */

        .login-wrapper {
            position: absolute;

            top: 135px;
            left: 0;
            right: 0;
            bottom: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 25px 20px 55px;

            z-index: 10;
        }


        /* =========================================================
           LOGIN CARD
        ========================================================= */

        .login-container {
            width: 100%;
            max-width: 505px;

            background: #ffffff;

            border-radius: 20px;

            overflow: hidden;

            box-shadow:
                0 24px 55px rgba(0, 0, 0, 0.28);

            animation: fadeInUp 0.45s ease-out;
        }


        /* Animasi */

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* =========================================================
           LOGIN HEADER
        ========================================================= */

        .login-header {
            padding: 32px 30px;

            text-align: center;

            color: #ffffff;

            background:
                linear-gradient(
                    135deg,
                    #1d4ed8 0%,
                    #2563eb 55%,
                    #3478e5 100%
                );
        }


        /* Icon */

        .login-icon {
            width: 66px;
            height: 66px;

            margin: 0 auto 14px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 16px;

            border: 1px solid rgba(255, 255, 255, 0.30);

            background: rgba(255, 255, 255, 0.13);

            box-shadow:
                0 8px 18px rgba(0, 0, 0, 0.12);

            font-size: 30px;
        }


        /* Judul */

        .login-header h4 {
            margin: 0 0 7px;

            font-size: 25px;

            font-weight: 700;

            letter-spacing: -0.3px;
        }


        /* Subjudul */

        .login-header p {
            margin: 0;

            font-size: 14px;

            opacity: 0.93;
        }


        /* =========================================================
           LOGIN BODY
        ========================================================= */

        .login-body {
            padding: 34px 40px 38px;

            background: #ffffff;
        }


        /* =========================================================
           LABEL
        ========================================================= */

        .form-label {
            display: block;

            margin-bottom: 8px;

            color: #1f2937;

            font-size: 14px;

            font-weight: 600;
        }


        .text-danger {
            color: #dc2626 !important;
        }


        /* =========================================================
           INPUT GROUP
        ========================================================= */

        .input-group {
            margin-bottom: 20px;
        }


        /* Icon input */

        .input-group-text {
            width: 50px;

            min-height: 49px;

            padding: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            color: #2563eb;

            background: #f8fafc;

            border: 1.5px solid #d6deea;

            border-right: 0;

            border-radius: 10px 0 0 10px;

            font-size: 17px;
        }


        /* Input */

        .form-control {
            min-height: 49px;

            padding: 11px 15px;

            color: #111827;

            background: #ffffff;

            border: 1.5px solid #d6deea;

            border-left: 0;

            border-radius: 0 10px 10px 0;

            font-size: 14px;

            box-shadow: none;

            transition: 0.2s ease;
        }


        .form-control::placeholder {
            color: #9ca3af;
        }


        .form-control:focus {
            color: #111827;

            background: #ffffff;

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.10);

            outline: none;
        }


        .input-group:focus-within .input-group-text {
            color: #1d4ed8;

            background: #eff6ff;

            border-color: #2563eb;
        }


        /* =========================================================
           REMEMBER ME
        ========================================================= */

        .remember-wrapper {
            margin-bottom: 23px;
        }


        .form-check {
            min-height: auto;

            margin: 0;

            padding-left: 0;

            display: flex;

            align-items: center;
        }


        .form-check-input {
            width: 19px;
            height: 19px;

            margin: 0 9px 0 0;

            border: 1.5px solid #cbd5e1;

            border-radius: 5px;

            cursor: pointer;

            box-shadow: none;
        }


        .form-check-input:checked {
            background-color: #2563eb;

            border-color: #2563eb;
        }


        .form-check-input:focus {
            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.12);
        }


        .form-check-label {
            color: #4b5563;

            font-size: 14px;

            cursor: pointer;

            user-select: none;
        }


        /* =========================================================
           BUTTON LOGIN
        ========================================================= */

        .btn-login {
            width: 100%;

            min-height: 49px;

            padding: 12px 20px;

            display: flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            color: #ffffff;

            background:
                linear-gradient(
                    135deg,
                    #1d4ed8,
                    #2563eb
                );

            border: none;

            border-radius: 10px;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            box-shadow:
                0 6px 16px rgba(37, 99, 235, 0.25);

            transition: all 0.2s ease;
        }


        .btn-login:hover {
            color: #ffffff;

            background:
                linear-gradient(
                    135deg,
                    #1e40af,
                    #1d4ed8
                );

            transform: translateY(-2px);

            box-shadow:
                0 8px 20px rgba(37, 99, 235, 0.34);
        }


        .btn-login:active {
            transform: translateY(0);
        }


        /* =========================================================
           ERROR
        ========================================================= */

        .alert {
            margin-bottom: 20px;

            padding: 12px 14px;

            border: none;

            border-radius: 10px;

            font-size: 13px;
        }


        .alert-danger {
            color: #991b1b;

            background: #fef2f2;

            border-left: 4px solid #dc2626;
        }


        .alert ul {
            margin: 7px 0 0;

            padding-left: 20px;
        }


        .alert li {
            margin-bottom: 3px;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .login-footer {
            position: absolute;

            left: 0;
            right: 0;

            bottom: 12px;

            text-align: center;

            color: rgba(255, 255, 255, 0.88);

            font-size: 12px;

            z-index: 20;

            pointer-events: none;
        }


        /* =========================================================
           TABLET
        ========================================================= */

        @media (max-width: 992px) {

            .logo-header {
                height: 120px;

                padding: 15px 35px;
            }


            .login-wrapper {
                top: 120px;

                padding: 20px 20px 45px;
            }


            .logo-left-group {
                gap: 20px;
            }


            .logo-jaya-raya,
            .logo-dprkp {
                height: 65px;

                max-width: 145px;
            }


            .logo-uprs {
                height: 75px;

                max-width: 165px;
            }
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 768px) {

            .logo-header {
                height: 145px;

                padding: 12px 20px;
            }


            .login-wrapper {
                top: 145px;

                padding: 15px 15px 45px;
            }


            .logo-left-group {
                gap: 12px;
            }


            .logo-jaya-raya,
            .logo-dprkp {
                height: 54px;

                max-width: 115px;
            }


            .logo-uprs {
                height: 64px;

                max-width: 135px;
            }


            .login-container {
                max-width: 450px;

                border-radius: 17px;
            }


            .login-header {
                padding: 27px 25px;
            }


            .login-icon {
                width: 58px;
                height: 58px;

                font-size: 26px;
            }


            .login-header h4 {
                font-size: 21px;
            }


            .login-header p {
                font-size: 13px;
            }


            .login-body {
                padding: 28px 25px 30px;
            }
        }


        /* =========================================================
           HP KECIL
        ========================================================= */

        @media (max-width: 576px) {

            .logo-header {
                height: 155px;

                padding: 10px 12px;

                flex-direction: column;

                justify-content: center;

                gap: 8px;
            }


            .login-wrapper {
                top: 155px;

                padding: 10px 12px 42px;
            }


            .logo-left-group {
                gap: 12px;
            }


            .logo-jaya-raya,
            .logo-dprkp {
                height: 48px;

                max-width: 100px;
            }


            .logo-uprs {
                height: 55px;

                max-width: 120px;
            }


            .login-container {
                border-radius: 15px;
            }


            .login-header {
                padding: 22px 18px;
            }


            .login-icon {
                width: 53px;
                height: 53px;

                margin-bottom: 11px;

                font-size: 24px;
            }


            .login-header h4 {
                font-size: 18px;

                margin-bottom: 5px;
            }


            .login-header p {
                font-size: 11px;
            }


            .login-body {
                padding: 23px 18px 25px;
            }


            .form-label {
                font-size: 13px;
            }


            .form-control,
            .input-group-text {
                min-height: 45px;

                font-size: 13px;
            }


            .input-group-text {
                width: 45px;
            }


            .form-check-label {
                font-size: 13px;
            }


            .btn-login {
                min-height: 46px;

                font-size: 14px;
            }


            .login-footer {
                bottom: 7px;

                font-size: 10px;
            }
        }


        /* =========================================================
           LAYAR PENDEK
           Agar tidak muncul scrollbar pada laptop dengan
           tinggi layar kecil.
        ========================================================= */

        @media (max-height: 760px) and (min-width: 577px) {

            .logo-header {
                height: 105px;

                padding-top: 10px;
                padding-bottom: 10px;
            }


            .logo-jaya-raya,
            .logo-dprkp {
                height: 58px;
            }


            .logo-uprs {
                height: 68px;
            }


            .login-wrapper {
                top: 105px;

                padding-top: 10px;

                padding-bottom: 38px;
            }


            .login-header {
                padding: 22px 25px;
            }


            .login-icon {
                width: 54px;
                height: 54px;

                margin-bottom: 9px;

                font-size: 25px;
            }


            .login-header h4 {
                font-size: 21px;
            }


            .login-header p {
                font-size: 12px;
            }


            .login-body {
                padding: 25px 32px 28px;
            }


            .input-group {
                margin-bottom: 15px;
            }


            .input-group-text,
            .form-control {
                min-height: 44px;
            }


            .remember-wrapper {
                margin-bottom: 17px;
            }


            .btn-login {
                min-height: 45px;
            }


            .login-footer {
                bottom: 7px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================================================
         HEADER / 3 LOGO
    ========================================================== -->
    <header class="logo-header">

        <!-- LOGO KIRI -->
        <div class="logo-left-group">

            <img
                src="{{ asset('images/logo/Logo Jaya Raya.png') }}"
                alt="Logo Jaya Raya"
                class="logo-jaya-raya"
            >

            <img
                src="{{ asset('images/logo/Logo DPRKP.png') }}"
                alt="Logo DPRKP"
                class="logo-dprkp"
            >

        </div>


        <!-- LOGO KANAN -->
        <div class="logo-right">

            <img
                src="{{ asset('images/logo/Logo UPRS VI.png') }}"
                alt="Logo UPRS VI"
                class="logo-uprs"
            >

        </div>

    </header>


    <!-- =========================================================
         LOGIN WRAPPER
    ========================================================== -->
    <div class="login-wrapper">

        <!-- =====================================================
             LOGIN CARD
        ====================================================== -->
        <main class="login-container">

            <!-- HEADER LOGIN -->
            <div class="login-header">

                <div class="login-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <h4>SI Inventory Sarpras</h4>

                <p>Unit Pengelola Rumah Susun VI</p>

            </div>


            <!-- BODY LOGIN -->
            <div class="login-body">

                <!-- ERROR LOGIN -->
                @if ($errors->any())

                    <div class="alert alert-danger">

                        <strong>
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Terjadi Kesalahan!
                        </strong>

                        <ul>

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- FORM LOGIN -->
                <form method="POST" action="{{ route('login') }}">

                    @csrf


                    <!-- EMAIL -->
                    <div class="mb-3">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email
                            <span class="text-danger">*</span>
                        </label>


                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-envelope-fill"></i>
                            </span>


                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email Anda"
                                autocomplete="email"
                                required
                                autofocus
                            >

                        </div>

                    </div>


                    <!-- PASSWORD -->
                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Password
                            <span class="text-danger">*</span>
                        </label>


                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>


                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Masukkan password Anda"
                                autocomplete="current-password"
                                required
                            >

                        </div>

                    </div>


                    <!-- INGAT SAYA -->
                    <div class="remember-wrapper">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >


                            <label
                                class="form-check-label"
                                for="remember"
                            >
                                Ingat Saya
                            </label>

                        </div>

                    </div>


                    <!-- TOMBOL MASUK -->
                    <button
                        type="submit"
                        class="btn-login"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk
                    </button>

                </form>

            </div>

        </main>

    </div>


    <!-- =========================================================
         FOOTER
    ========================================================== -->
    <footer class="login-footer">
        &copy; {{ date('Y') }} SI Inventory Sarpras UPRS VI
    </footer>


    <!-- Bootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>