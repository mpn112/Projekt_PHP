<?php
require_once "auth.php";
require_once "db.php";

$pol = db_polacz();
$msg = "";
$err = "";

if (isset($_POST["akcja"]) && $_POST["akcja"] === "login") {
    $login = pobierz_post("login");
    $haslo = pobierz_post("haslo");

    if ($login == "" || $haslo == "") {
        $err = "Podaj login i hasło.";
    } else {
        $login_esc = mysqli_real_escape_string($pol, $login);
        $sql = "SELECT haslo_hash FROM uzytkownicy WHERE login='$login_esc'";
        $res = mysqli_query($pol, $sql);

        if ($res && mysqli_num_rows($res) == 1) {
            $row = mysqli_fetch_row($res);
            $hash = $row[0];

            if (password_verify($haslo, $hash)) {
                $_SESSION["login"] = $login;
                header("Location: index.php");
                exit();
            } else {
                $err = "Błędne hasło.";
            }
        } else {
            $err = "Nie ma takiego użytkownika.";
        }

        if ($res) mysqli_free_result($res);
    }
}

mysqli_close($pol);
include "header.php";
?>
<div class="card">
  <h2>Logowanie</h2>

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
    <p>
      <button class="btn" type="submit" name="akcja" value="login">Zaloguj</button>
      <a class="btn" href="rejestracja.php" style="text-decoration:none; display:inline-block;">Rejestracja</a>
    </p>
  </form>
</div>
<?php include "footer.php"; ?>