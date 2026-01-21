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
            $id_pojazdu = (int)pobierz_post("id_pojazdu");
            $id_mechanika = (int)pobierz_post("id_mechanika");
            $opis = pobierz_post("opis_usterki");
            $status = pobierz_post("status");
            $data_p = pobierz_post("data_przyjecia");
            $data_z = pobierz_post("data_zakonczenia");
            $koszt = pobierz_post("koszt");

            if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || $status=="" || $data_p=="" || $koszt=="") {
                $err = "Uzupełnij wymagane pola naprawy.";
            } else {
                $data_z_sql = "NULL";
                if ($data_z != "") $data_z_sql = "'".mysqli_real_escape_string($pol,$data_z)."'";

                $sql = "INSERT INTO naprawy(id_pojazdu,id_mechanika,opis_usterki,status,data_przyjecia,data_zakonczenia,koszt)
                        VALUES ($id_pojazdu,$id_mechanika,'"
                        .mysqli_real_escape_string($pol,$opis)."','"
                        .mysqli_real_escape_string($pol,$status)."','"
                        .mysqli_real_escape_string($pol,$data_p)."',"
                        .$data_z_sql.","
                        .(float)$koszt.")";
                mysqli_query($pol, $sql);
                $msg = "Dodano naprawę.";
            }
            break;

        case "usun":
            $id = (int)pobierz_post("id");
            mysqli_query($pol, "DELETE FROM naprawy WHERE id_naprawy=$id");
            $msg = "Usunięto naprawę.";
            break;

        case "zapisz":
            $id = (int)pobierz_post("id");
            $id_pojazdu = (int)pobierz_post("id_pojazdu");
            $id_mechanika = (int)pobierz_post("id_mechanika");
            $opis = pobierz_post("opis_usterki");
            $status = pobierz_post("status");
            $data_p = pobierz_post("data_przyjecia");
            $data_z = pobierz_post("data_zakonczenia");
            $koszt = pobierz_post("koszt");

            if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || $status=="" || $data_p=="" || $koszt=="") {
                $err = "Uzupełnij wymagane pola naprawy.";
            } else {
                $data_z_sql = "NULL";
                if ($data_z != "") $data_z_sql = "'".mysqli_real_escape_string($pol,$data_z)."'";

                $sql = "UPDATE naprawy SET id_pojazdu=$id_pojazdu, id_mechanika=$id_mechanika,
                        opis_usterki='".mysqli_real_escape_string($pol,$opis)."',
                        status='".mysqli_real_escape_string($pol,$status)."',
                        data_przyjecia='".mysqli_real_escape_string($pol,$data_p)."',
                        data_zakonczenia=".$data_z_sql.",
                        koszt=".(float)$koszt."
                        WHERE id_naprawy=$id";
                mysqli_query($pol, $sql);
                $msg = "Zapisano zmiany.";
            }
            break;
    }
}

$szukaj = pobierz_get("q");  // wyszukiwarka przez GET
include "header.php";
?>
<div class="card">
  <h2>Naprawy</h2>
  <?php if ($msg!="") { ?><div class="msg"><?php echo h($msg); ?></div><?php } ?>
  <?php if ($err!="") { ?><div class="err"><?php echo h($err); ?></div><?php } ?>

  <h3>Wyszukiwanie</h3>
  <form method="get">
    Szukaj (nazwisko / VIN / rejestracja):
    <input type="text" name="q" value="<?php echo h($szukaj); ?>">
    <p><button class="btn" type="submit">Szukaj</button> <a href="naprawy.php">Wyczyść</a></p>
  </form>

  <h3>Dodaj naprawę</h3>
  <form method="post">
    Pojazd:
    <select name="id_pojazdu">
      <?php
        $rp = mysqli_query($pol, "SELECT p.id_pojazdu, p.rejestracja, p.vin, k.nazwisko, k.imie, p.marka, p.model
                                  FROM pojazdy p JOIN klienci k ON p.id_klienta=k.id_klienta
                                  ORDER BY p.id_pojazdu DESC");
        while ($rp && ($p = mysqli_fetch_row($rp))) {
            echo "<option value='".h($p[0])."'>ID ".h($p[0])." | ".h($p[6])." ".h($p[5])." | ".h($p[1])." | ".h($p[3])." ".h($p[4])."</option>";
        }
        if ($rp) mysqli_free_result($rp);
      ?>
    </select>

    Mechanik:
    <select name="id_mechanika">
      <?php
        $rm = mysqli_query($pol, "SELECT id_mechanika, nazwisko, imie, specjalizacja FROM mechanicy ORDER BY nazwisko");
        while ($rm && ($m = mysqli_fetch_row($rm))) {
            echo "<option value='".h($m[0])."'>ID ".h($m[0])." | ".h($m[1])." ".h($m[2])." | ".h($m[3])."</option>";
        }
        if ($rm) mysqli_free_result($rm);
      ?>
    </select>

    Status:
    <?php
      $statusy = array("Przyjęte", "W trakcie", "Gotowe", "Wydane");
    ?>
    <select name="status">
      <?php
        for ($i=0; $i<count($statusy); $i++) {
            echo "<option value='".h($statusy[$i])."'>".h($statusy[$i])."</option>";
        }
      ?>
    </select>

    Opis usterki:
    <textarea name="opis_usterki"></textarea>

    <div class="grid2">
      <div>Data przyjęcia: <input type="date" name="data_przyjecia" value="<?php echo date('Y-m-d'); ?>"></div>
      <div>Data zakończenia: <input type="date" name="data_zakonczenia"></div>
    </div>

    Koszt: <input type="number" step="0.01" name="koszt" value="0.00">

    <p><button class="btn" type="submit" name="akcja" value="dodaj">Dodaj</button></p>
  </form>

  <h3>Lista napraw</h3>
  <table>
    <tr>
      <th>ID</th><th>Pojazd</th><th>Klient</th><th>Mechanik</th>
      <th>Status</th><th>Daty</th><th>Koszt</th><th>Opis</th><th>Akcje</th>
    </tr>
    <?php
      $where = "";
      if ($szukaj != "") {
          $q = mysqli_real_escape_string($pol, $szukaj);
          $where = "WHERE k.nazwisko LIKE '%$q%' OR p.vin LIKE '%$q%' OR p.rejestracja LIKE '%$q%'";
      }

      $sql = "SELECT n.id_naprawy,
                     p.id_pojazdu, p.marka, p.model, p.rejestracja, p.vin,
                     k.nazwisko, k.imie,
                     m.id_mechanika, m.nazwisko, m.imie,
                     n.status, n.data_przyjecia, n.data_zakonczenia, n.koszt, n.opis_usterki
              FROM naprawy n
              JOIN pojazdy p ON n.id_pojazdu=p.id_pojazdu
              JOIN klienci k ON p.id_klienta=k.id_klienta
              JOIN mechanicy m ON n.id_mechanika=m.id_mechanika
              $where
              ORDER BY n.id_naprawy DESC";

      $res = mysqli_query($pol, $sql);
      while ($res && ($r = mysqli_fetch_row($res))) {
        echo "<tr><form method='post'>";
        echo "<td>".h($r[0])."<input type='hidden' name='id' value='".h($r[0])."'></td>";
        echo "<td>
                ID pojazdu: <input type='number' name='id_pojazdu' value='".h($r[1])."'><br>
                ".h($r[2])." ".h($r[3])." | ".h($r[4])."<br><small>VIN: ".h($r[5])."</small>
              </td>";
        echo "<td>".h($r[6])." ".h($r[7])."</td>";
        echo "<td>
                ID mech.: <input type='number' name='id_mechanika' value='".h($r[8])."'><br>
                ".h($r[9])." ".h($r[10])."
              </td>";

        echo "<td><input type='text' name='status' value='".h($r[11])."'></td>";
        echo "<td>
                <small>Przyjęcie</small><input type='date' name='data_przyjecia' value='".h($r[12])."'>
                <small>Zakończenie</small><input type='date' name='data_zakonczenia' value='".h($r[13])."'>
              </td>";
        echo "<td><input type='number' step='0.01' name='koszt' value='".h($r[14])."'></td>";
        echo "<td><textarea name='opis_usterki'>".h($r[15])."</textarea></td>";
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
