<?php
require_once "auth.php";
wymagaj_logowania();
require_once "db.php";

$pol = db_polacz();
$msg = "";
$err = "";

$akcja = pobierz_post("akcja");

if ($akcja != "") {
    switch ($akcja) {
        case "dodaj":
            $imie = pobierz_post("imie");
            $nazwisko = pobierz_post("nazwisko");
            $spec = pobierz_post("specjalizacja");

            if ($imie=="" || $nazwisko=="" || $spec=="") {
                $err = "Uzupełnij wszystkie pola mechanika.";
            } else {
                $sql = "INSERT INTO mechanicy(imie,nazwisko,specjalizacja) VALUES ('"
                    .mysqli_real_escape_string($pol,$imie)."','"
                    .mysqli_real_escape_string($pol,$nazwisko)."','"
                    .mysqli_real_escape_string($pol,$spec)."')";
                mysqli_query($pol, $sql);
                $msg = "Dodano mechanika.";
            }
            break;

        case "usun":
            $id = (int)pobierz_post("id");
            mysqli_query($pol, "DELETE FROM mechanicy WHERE id_mechanika=$id");
            $msg = "Usunięto mechanika.";
            break;

        case "zapisz":
            $id = (int)pobierz_post("id");
            $imie = pobierz_post("imie");
            $nazwisko = pobierz_post("nazwisko");
            $spec = pobierz_post("specjalizacja");

            if ($imie=="" || $nazwisko=="" || $spec=="") {
                $err = "Uzupełnij wszystkie pola mechanika.";
            } else {
                $sql = "UPDATE mechanicy SET imie='"
                    .mysqli_real_escape_string($pol,$imie)
                    ."', nazwisko='".mysqli_real_escape_string($pol,$nazwisko)
                    ."', specjalizacja='".mysqli_real_escape_string($pol,$spec)
                    ."' WHERE id_mechanika=$id";
                mysqli_query($pol, $sql);
                $msg = "Zapisano zmiany.";
            }
            break;
    }
}

include "header.php";
?>
<div class="card">
  <h2>Mechanicy</h2>
  <?php if ($msg!="") { ?><div class="msg"><?php echo h($msg); ?></div><?php } ?>
  <?php if ($err!="") { ?><div class="err"><?php echo h($err); ?></div><?php } ?>

  <h3>Dodaj mechanika</h3>
  <form method="post">
    <div class="grid2">
      <div>Imię: <input type="text" name="imie"></div>
      <div>Nazwisko: <input type="text" name="nazwisko"></div>
    </div>
    Specjalizacja: <input type="text" name="specjalizacja">
    <p><button class="btn" type="submit" name="akcja" value="dodaj">Dodaj</button></p>
  </form>

  <h3>Lista</h3>
  <table>
    <tr><th>ID</th><th>Imię</th><th>Nazwisko</th><th>Specjalizacja</th><th>Akcje</th></tr>
    <?php
      $res = mysqli_query($pol, "SELECT id_mechanika, imie, nazwisko, specjalizacja FROM mechanicy ORDER BY id_mechanika DESC");
      while ($res && ($row = mysqli_fetch_row($res))) {
        $id = $row[0];
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".h($id)."<input type='hidden' name='id' value='".h($id)."'></td>";
        echo "<td><input type='text' name='imie' value='".h($row[1])."'></td>";
        echo "<td><input type='text' name='nazwisko' value='".h($row[2])."'></td>";
        echo "<td><input type='text' name='specjalizacja' value='".h($row[3])."'></td>";
        echo "<td>
                <button class='btn' type='submit' name='akcja' value='zapisz'>Zapisz</button>
                <button class='btn' type='submit' name='akcja' value='usun' onclick=\"return confirm('Usunąć?')\">Usuń</button>
              </td>";
        echo "</form>";
        echo "</tr>";
      }
      if ($res) mysqli_free_result($res);
    ?>
  </table>
</div>
<?php
mysqli_close($pol);
include "footer.php";
?>
