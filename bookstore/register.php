<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrasi</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial,sans-serif;
}
body {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
}
.bg {
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(60,20,0,0.75), rgba(60,20,0,0.75)),
        url('library.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
}
.form-box {
    width: 360px;
    padding: 32px 28px;
    border-radius: 24px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}
.icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 26px;
    border-radius: 50%;
    background: #2F5678;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}
.field {
    margin-bottom: 18px;
}
.field label {
    display: block;
    margin-bottom: 8px;
    margin-left: 10px;
    color: white;
    font-size: 14px;
    font-weight: 500;
}
.field input {
    width: 100%;
    height: 52px;
    padding: 0 18px;
    border: 2px solid rgba(255,255,255,0.95);
    border-radius: 16px;
    background: rgba(255,255,255,0.12);
    color: white;
    font-size: 15px;
    outline: none;
}
.field input::placeholder {
    color: #EAD9C7;
}
.btn {
    width: 100%;
    height: 50px;
    border: none;
    border-radius: 26px;
    background: #F5E6D3;
    color: #4A2410;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}
.btn:hover {
    opacity: 0.95;
}
</style>
<link rel="stylesheet" href="simple.css">
</head>

<body>

<div class="bg">

   <form class="form-box" action="proses_register.php" method="POST">

        <div class="icon">
            <i class="fa-solid fa-user-plus"></i>
        </div>

        <div class="field">
            <label>Nama</label>
            <input type="text" name="nama" required>
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="field">
            <label>Alamat</label>
            <input type="text" name="alamat" required>
        </div>

        <div class="field">
            <label>Nomor Telepon</label>
            <input type="text" name="telepon" required>
        </div>

        <div class="field">
            <label>Kata Sandi</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn" type="submit">REGISTRASI</button>

    </form>

</div>

</body>
</html>