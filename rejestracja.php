<?php
require_once "auth.php";
require_once "db.php";

$pol = db_polacz();
$msg = "";
$err = "";

if (isset($_POST["akcja"])) {
    $akcja = $_POST["akcja"];

    if ($akcja === "rejestruj") {
        $login = pobierz_post("login");
        $haslo = pobierz_post("haslo");
        $haslo2 = pobierz_post("haslo2");

        if ($login == "" || $haslo == "" || $haslo2 == "") {
            $err = "Uzupełnij wszystkie pola.";
        } elseif ($haslo !== $haslo2) {
            $err = "Hasła nie są takie same.";
        } elseif (strlen($haslo) < 6) {
            $err = "Hasło musi mieć co najmniej 6 znaków.";
        } else {
            $login_esc = mysqli_real_escape_string($pol, $login);

            // Czy login już istnieje?
            $chk = mysqli_query($pol, "SELECT id_uzytkownika FROM uzytkownicy WHERE login='$login_esc' LIMIT 1");
            $istnieje = ($chk && mysqli_num_rows($chk) > 0);
            if ($chk) mysqli_free_result($chk);

            if ($istnieje) {
                $err = "Taki login już istnieje.";
            } else {
                $hash = password_hash($haslo, PASSWORD_DEFAULT);
                $hash_esc = mysqli_real_escape_string($pol, $hash);

                $sql = "INSERT INTO uzytkownicy(login, haslo_hash) VALUES ('$login_esc', '$hash_esc')";
                mysqli_query($pol, $sql);

                $msg = "Konto utworzone. Możesz się zalogować.";
            }
        }
    }

    // Opcjonalnie: konto testowe jak wcześniej
    if ($akcja === "utworz_test") {
        $login = "admin";
        $haslo = "admin123";
        $hash = password_hash($haslo, PASSWORD_DEFAULT);

        $sql = "INSERT IGNORE INTO uzytkownicy(login, haslo_hash)
                VALUES ('".mysqli_real_escape_string($pol,$login)."','".mysqli_real_escape_string($pol,$hash)."')";
        mysqli_query($pol, $sql);

        $msg = "Utworzono konto testowe: login=admin, hasło=admin123 (jeśli nie istniało).";
    }
}

mysqli_close($pol);
include "header.php";
?>
<div class="card">
  <h2>Rejestracja</h2>

  <?php if ($msg != "") { ?><div class="msg"><?php echo h($msg); ?></div><?php } ?>
  <?php if ($err != "") { ?><div class="err"><?php echo h($err); ?></div><?php } ?>

  <form method="post">
    <div class="grid2">
      <div>
        Login:
        <input type="text" name="login">
      </div>
      <div>
        Hasło:
        <input type="password" name="haslo">
      </div>
    </div>

    <div class="grid2">
      <div>
        Powtórz hasło:
        <input type="password" name="haslo2">
      </div>
      <div></div>
    </div>

    <p>
      <button class="btn" type="submit" name="akcja" value="rejestruj">Zarejestruj</button>
      <a class="btn" href="login.php" style="text-decoration:none; display:inline-block;">Wróć do logowania</a>
    </p>

    <hr>
    <p>
      <button class="btn" type="submit" name="akcja" value="utworz_test">Utwórz konto testowe</button>
    </p>
  </form>
</div>
<?php include "footer.php"; ?>
