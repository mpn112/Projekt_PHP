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
            $id_klienta = (int)pobierz_post("id_klienta");
            $marka = pobierz_post("marka");
            $model = pobierz_post("model");
            $rok = (int)pobierz_post("rok");
            $vin = pobierz_post("vin");
            $rej = pobierz_post("rejestracja");

            if ($id_klienta<=0 || $marka=="" || $model=="" || $rok<=0 || $vin=="" || $rej=="") {
                $err = "Uzupełnij wszystkie pola pojazdu.";
            } else {
                $sql = "INSERT INTO pojazdy(id_klienta, marka, model, rok, vin, rejestracja) VALUES ("
                    .$id_klienta.", '"
                    .mysqli_real_escape_string($pol,$marka)."','"
                    .mysqli_real_escape_string($pol,$model)."',"
                    .$rok.", '"
                    .mysqli_real_escape_string($pol,$vin)."','"
                    .mysqli_real_escape_string($pol,$rej)."')";
                mysqli_query($pol, $sql);
                $msg = "Dodano pojazd.";
            }
            break;

        case "usun":
            $id = (int)pobierz_post("id");
            $q = mysqli_query($pol, "SELECT COUNT(*) FROM naprawy WHERE id_pojazdu=$id");
            $cnt = 0;
            if ($q) {
                $r = mysqli_fetch_row($q);
                $cnt = (int)($r[0] ?? 0);
                mysqli_free_result($q);
            }

            if ($cnt > 0) {
                $err = "Nie można usunąć pojazdu — istnieje powiązana naprawa.";
            } else {
                mysqli_query($pol, "DELETE FROM pojazdy WHERE id_pojazdu=$id");
                $msg = "Usunięto pojazd.";
            }
            break;

        case "zapisz":
            $id = (int)pobierz_post("id");
            $id_klienta = (int)pobierz_post("id_klienta");
            $marka = pobierz_post("marka");
            $model = pobierz_post("model");
            $rok = (int)pobierz_post("rok");
            $vin = pobierz_post("vin");
            $rej = pobierz_post("rejestracja");

            if ($id_klienta<=0 || $marka=="" || $model=="" || $rok<=0 || $vin=="" || $rej=="") {
                $err = "Uzupełnij wszystkie pola pojazdu.";
            } else {
                $sql = "UPDATE pojazdy SET id_klienta=$id_klienta, marka='"
                    .mysqli_real_escape_string($pol,$marka)
                    ."', model='".mysqli_real_escape_string($pol,$model)
                    ."', rok=$rok, vin='".mysqli_real_escape_string($pol,$vin)
                    ."', rejestracja='".mysqli_real_escape_string($pol,$rej)
                    ."' WHERE id_pojazdu=$id";
                mysqli_query($pol, $sql);
                $msg = "Zapisano zmiany.";
            }
            break;
    }
}

include "header.php";
?>
<div class="card">
  <h2>Pojazdy</h2>
  <?php if ($msg!="") { ?><div class="msg"><?php echo h($msg); ?></div><?php } ?>
  <?php if ($err!="") { ?><div class="err"><?php echo h($err); ?></div><?php } ?>

  <h3>Dodaj pojazd</h3>
  <form method="post">
    Klient:
    <select name="id_klienta">
      <?php
        $rk = mysqli_query($pol, "SELECT id_klienta, imie, nazwisko FROM klienci ORDER BY nazwisko");
        while ($rk && ($k = mysqli_fetch_row($rk))) {
            echo "<option value='".h($k[0])."'>".h($k[2])." ".h($k[1])." (ID ".h($k[0]).")</option>";
        }
        if ($rk) mysqli_free_result($rk);
      ?>
    </select>
    <div class="grid2">
      <div>Marka: <input type="text" name="marka"></div>
      <div>Model: <input type="text" name="model"></div>
    </div>
    <div class="grid2">
      <div>Rok: <input type="number" name="rok"></div>
      <div>VIN: <input type="text" name="vin"></div>
    </div>
    Rejestracja: <input type="text" name="rejestracja">
    <p><button class="btn" type="submit" name="akcja" value="dodaj">Dodaj</button></p>
  </form>

  <h3>Lista</h3>
  <table>
    <tr><th>ID</th><th>Klient</th><th>Marka</th><th>Model</th><th>Rok</th><th>VIN</th><th>Rej.</th><th>Akcje</th></tr>
    <?php
      $sql = "SELECT p.id_pojazdu, p.id_klienta, k.nazwisko, k.imie, p.marka, p.model, p.rok, p.vin, p.rejestracja
              FROM pojazdy p JOIN klienci k ON p.id_klienta=k.id_klienta
              ORDER BY p.id_pojazdu DESC";
      $res = mysqli_query($pol, $sql);
      while ($res && ($row = mysqli_fetch_row($res))) {
        echo "<tr><form method='post'>";
        echo "<td>".h($row[0])."<input type='hidden' name='id' value='".h($row[0])."'></td>";
        echo "<td><input type='number' name='id_klienta' value='".h($row[1])."'><br><small>".h($row[2])." ".h($row[3])."</small></td>";
        echo "<td><input type='text' name='marka' value='".h($row[4])."'></td>";
        echo "<td><input type='text' name='model' value='".h($row[5])."'></td>";
        echo "<td><input type='number' name='rok' value='".h($row[6])."'></td>";
        echo "<td><input type='text' name='vin' value='".h($row[7])."'></td>";
        echo "<td><input type='text' name='rejestracja' value='".h($row[8])."'></td>";
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
