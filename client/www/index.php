<?php

require_once("php/config.php");

/*if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}*/


echo shell_exec(escapeshellcmd("whoami"));
echo shell_exec(escapeshellcmd("sudo python3 /var/www/html/scripts/test_temp.py"));
echo shell_exec("ls");

?>
<html>

<head>
    <title>Face Recognition</title>

    <meta charset="utf-8">
    <link rel="icon" src="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <link href="dist/style.css" rel="stylesheet">
</head>

<body>

    <main class="container mb-5">
        <div class="row">
            <div class="col-12">
                <!--BEGIN TITLE-->
                <h1>Utenti</h1>
                <!--END TITLE-->
                <div class="row">
                    <div>
                        <button type="button" class="btn btn-primary" id="addTaxModalButton">
                            <i class="bi bi-plus"></i>Aggiungi Utente
                        </button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="span12 table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Cognome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="taxTable">

                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            </div>
        </div>

        <!-- Modal tasse -->
        <div class="modal fade" id="addTaxModal" tabindex="-1" aria-labelledby="addTaxModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addTaxModalLabel">Aggiungi Tassa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tax_name" class="form-label">Nome Tassa</label>
                            <input type="text" class="form-control" id="tax_name" placeholder="Nome Tassa">
                        </div>
                        <div class="mb-3">
                            <label for="tax_date" class="form-label">Data</label>
                            <input type="date" class="form-control" id="tax_date" placeholder="Data">
                        </div>
                        <div class="mb-3">
                            <label for="tax_subtotal" class="form-label">Totale Tassa</label>
                            <input type="number" class="form-control" id="tax_amount" placeholder="Totale Tassa" min="1"
                                step="any">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="tax_paid" value="false">
                            <label class="form-check-label" for="tax_paid">Pagata</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="button" class="btn btn-primary" id="addTax">Aggiungi</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>

</html>