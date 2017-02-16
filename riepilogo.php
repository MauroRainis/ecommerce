<?php

// inizializziamo le sessioni
session_start();

include 'libs/db.php';

$db = creaConnessionePDO();

// recupero il carrello corrente
$carrello = $_SESSION['carrello'];

$utente = $_SESSION['utente'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title>MV chocosite</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
  </head>
  <body>
    <?php include 'include/header.php'; ?>
    <main>
      <div class="row">
        <div class="col-md-12">
          <h1>Riepilogo acquisti</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <table class="table table-hover">
            <thead>
            <tr>
              <th>Prodotto</th>
              <th>Quantità</th>
              <th>Prezzo unitario</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $totaleCarrello = 0;
            foreach($carrello as $rigaCarrello) {

              $stmt = $db->prepare('SELECT prodotti.nome, prodotti.prezzo, prodottivarianti.prezzo as prezzo_variante, varianti.nome as nome_variante 
                                    FROM prodottivarianti, varianti, prodotti
                                    WHERE prodottivarianti.variante_id = varianti.id
                                    AND prodotti.id = prodottivarianti.prodotto_id
                                    AND prodottivarianti.prodotto_id = :idProdotto
                                    AND prodottivarianti.variante_id = :idVariante');

              // bind parametro alla query
              $stmt->bindParam(':idProdotto', $rigaCarrello['prodotto'], PDO::PARAM_INT);
              $stmt->bindParam(':idVariante', $rigaCarrello['variante'], PDO::PARAM_INT);
              $stmt->execute();

              $risultato = $stmt->fetchAll(PDO::FETCH_ASSOC);

              // mi aspetto solo 1 riga come risultato
              $valori = $risultato[0];

              $prezzo = $valori['prezzo'] + $valori['prezzo_variante'];

              ?>
              <tr>
                <td><?= $valori['nome'] ?><br />
                  <small><?= $valori['nome_variante'] ?></small></td>
                <td>1</td>
                <td><?= $valori['prezzo'] ?> &euro;</td>
                <td><a href="" class="btn btn-link">rimuovi</a></td>
              </tr>
              <?php
              $totaleCarrello += $prezzo;
            }
            ?>
            <tr class="success" style="font-weight: bold">
              <th scope="row"></th>
              <td>Totale</td>
              <td></td>
              <td><?=$totaleCarrello?> &euro;</td>
              <td></td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-6">
          <?php $utente = $_SESSION['utente']; ?>
          <h3>Dati utente</h3>
          <p>
            <strong><?=$utente['nome'] . ' ' . $utente['cognome']?></strong><br>
            <?=$utente['indirizzo'] . ', ' . $utente['citta'] . ' - ' . $utente['cap'] . ' (' . $utente['provincia'] . ')'?><br>
            <?=$utente['email']?><br>
            Note: <?=$utente['note']?>
          </p>

        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
        </div>
        <div class="col-md-4">
          <a href="concludi_ordine.php" class="btn btn-success btn-lg">Concludi ordine</a>
        </div>
      </div>
    </main>
    <?php include 'include/footer.php'; ?>
  </body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
</html>
