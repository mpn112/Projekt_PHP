<?php
require_once "auth.php";
wymagaj_logowania();
require_once "db.php";

$pol = db_polacz();
$msg = ""; $err = "";
$akcja = pobierz_post("akcja");

if ($akcja != "") {
    switch ($akcja) {
        case "dodaj":
            $imie = pobierz_post("imie");
            $nazwisko = pobierz_post("nazwisko");
            $telefon = pobierz_post("telefon");
            $email = pobierz_post("email");
            if ($imie=="" || $nazwisko=="" || $telefon=="" || $email=="") {
                $err = "Uzupełnij wszystkie pola klienta.";
            } else {
                $sql = "INSERT INTO klienci(imie,nazwisko,telefon,email) VALUES ('"
                    .mysqli_real_escape_string($pol,$imie)."','"
                    .mysqli_real_escape_string($pol,$nazwisko)."','"
                    .mysqli_real_escape_string($pol,$telefon)."','"
                    .mysqli_real_escape_string($pol,$email)."')";
                mysqli_query($pol, $sql);
                $msg = "Dodano klienta.";
            }
            break;

        case "usun":
            $id = (int)pobierz_post("id");
            mysqli_query($pol, "DELETE FROM klienci WHERE id_klienta=$id");
            $msg = "Usunięto klienta (jeśli nie ma powiązanych pojazdów).";
            break;

        case "zapisz":
            $id = (int)pobierz_post("id");
            $imie = pobierz_post("imie");
            $nazwisko = pobierz_post("nazwisko");
            $telefon = pobierz_post("telefon");
            $email = pobierz_post("email");
            if ($imie=="" || $nazwisko=="" || $telefon=="" || $email=="") {
                $err = "Uzupełnij wszystkie pola klienta.";
            } else {
                $sql = "UPDATE klienci SET imie='"
                    .mysqli_real_escape_string($pol,$imie)
                    ."', nazwisko='".mysqli_real_escape_string($pol,$nazwisko)
                    ."', telefon='".mysqli_real_escape_string($pol,$telefon)
                    ."', email='".mysqli_real_escape_string($pol,$email)
                    ."' WHERE id_klienta=$id";
                mysqli_query($pol, $sql);
                $msg = "Zapisano zmiany.";
            }
            break;
    }
}

include "header.php";
?>
<div class="card">
  <h2>Klienci</h2>
  <?php if ($msg!="") { ?><div class="msg"><?php echo h($msg); ?></div><?php } ?>
  <?php if ($err!="") { ?><div class="err"><?php echo h($err); ?></div><?php } ?>

  <h3>Dodaj klienta</h3>
  <form method="post">
    <div class="grid2">
      <div>Imię: <input type="text" name="imie"></div>
      <div>Nazwisko: <input type="text" name="nazwisko"></div>
    </div>
    <div class="grid2">
      <div>Telefon: <input type="text" name="telefon"></div>
      <div>Email: <input type="text" name="email"></div>
    </div>
    <p><button class="btn" type="submit" name="akcja" value="dodaj">Dodaj</button></p>
  </form>

  <h3>Lista</h3>
  <table>
    <tr><th>ID</th><th>Imię</th><th>Nazwisko</th><th>Telefon</th><th>Email</th><th>Akcje</th></tr>
    <?php
      $res = mysqli_query($pol, "SELECT id_klienta, imie, nazwisko, telefon, email FROM klienci ORDER BY id_klienta DESC");
      while ($res && ($row = mysqli_fetch_row($res))) {
        $id = $row[0];
        echo "<tr><form method='post'>";
        echo "<td>".h($id)."<input type='hidden' name='id' value='".h($id)."'></td>";
        echo "<td><input type='text' name='imie' value='".h($row[1])."'></td>";
        echo "<td><input type='text' name='nazwisko' value='".h($row[2])."'></td>";
        echo "<td><input type='text' name='telefon' value='".h($row[3])."'></td>";
        echo "<td><input type='text' name='email' value='".h($row[4])."'></td>";
        echo "<td>
                <button class='btn' type='submit' name='akcja' value='zapisz'>Zapisz</button>
                <button class='btn' type='submit' name='akcja' value='usun' onclick=\"return confirm('Usunąć?')\">Usuń</button>
              </td>";
        echo "</form></tr>";
      }
      if ($res) mysqli_free_result($res);
    ?>
  </table>
</div>
<?php
mysqli_close($pol);
include "footer.php";
?>
