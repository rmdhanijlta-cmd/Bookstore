<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk - Mille Pagine</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root {
    --coklat: #5B2E0F;
    --coklat-tua: #4A2410;
    --krem: #F5E6D3;
    --krem-terang: #FFF3E8;
    --emas: #B98A55;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}
body {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
}
.bg {
    width: 100vw;
    height: 100vh;
    background-image: linear-gradient(rgba(74,36,16,0.85), rgba(91,46,15,0.85)),
        url('library.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
}
.form-box {
    width: 380px;
    padding: 36px 32px;
    border-radius: 24px;
    background: rgba(255, 243, 232, 0.08);
    border: 1.5px solid rgba(245, 230, 211, 0.25);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}
.brand-title {
    text-align: center;
    color: var(--krem);
    font-family: Georgia, serif;
    font-size: 24px;
    margin-bottom: 6px;
    letter-spacing: 1px;
}
.brand-title span {
    color: var(--emas);
}
.brand-subtitle {
    text-align: center;
    color: rgba(255,243,232,0.7);
    font-size: 13px;
    margin-bottom: 24px;
}
.icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: var(--emas);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 6px 16px rgba(185,138,85,0.3);
}
.field {
    margin-bottom: 18px;
}
.field label {
    display: block;
    margin-bottom: 8px;
    margin-left: 4px;
    color: var(--krem-terang);
    font-size: 13px;
    font-weight: bold;
}
.field input {
    width: 100%;
    height: 48px;
    padding: 0 16px;
    border: 1.5px solid rgba(245, 230, 211, 0.3);
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    color: white;
    font-size: 14px;
    outline: none;
    transition: all .2s ease;
}
.field input:focus {
    border-color: var(--emas);
    background: rgba(255,255,255,0.15);
    box-shadow: 0 0 12px rgba(185,138,85,0.2);
}
.field input::placeholder {
    color: rgba(234,217,199,0.6);
}
.btn {
    width: 100%;
    height: 48px;
    border: none;
    border-radius: 12px;
    background: var(--krem);
    color: var(--coklat-tua);
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    transition: all .2s ease;
}
.btn:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
.btn:active {
    transform: scale(0.98);
}
.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: rgba(255,243,232,0.7);
    font-size: 13px;
    text-decoration: none;
    transition: color .2s ease;
}
.back-link:hover {
    color: white;
}
</style>
<link rel="stylesheet" href="simple.css">
</head>

<body>

<div class="bg">

    <form class="form-box" action="proses_login.php" method="POST">

        <div class="brand-title"><span>BOOK</span>STORE</div>
        <div class="brand-subtitle">Silakan masuk ke akun kamu</div>

        <div class="icon">
            <i class="fa-solid fa-right-to-bracket"></i>
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" placeholder="nama@email.com" required>
        </div>

        <div class="field">
            <label>Kata Sandi</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button class="btn" type="submit">Masuk</button>

        <a href="index.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>

    </form>

</div>

</body>
</html>