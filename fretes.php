
<?php
require __DIR__ . '/database/conexao.php';

$sql = "
    SELECT
        f.id,
        DATE_FORMAT(f.data_frete, '%d/%m/%Y') AS data,
        origem.identificacao AS origem,
        destino.identificacao AS destino,
        t.nome AS transportador,
        GROUP_CONCAT(
            p.nome ORDER BY p.nome SEPARATOR ', '
        ) AS produtos
    FROM fretes f
    INNER JOIN enderecos origem
        ON origem.id = f.origem_id
    INNER JOIN enderecos destino
        ON destino.id = f.destino_id
    INNER JOIN transportadores t
        ON t.id = f.transportador_id
    LEFT JOIN frete_produtos fp
        ON fp.frete_id = f.id
    LEFT JOIN produtos p
        ON p.id = fp.produto_id
    GROUP BY
        f.id, f.data_frete,
        origem.identificacao,
        destino.identificacao, t.nome
    ORDER BY f.id DESC
";

$consulta = $pdo->query($sql);
$fretes = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Fretes - Gerenciador Logístico</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Fretes</h2>

        <a href="novo-frete.php" class="btn btn-primary">
            + Novo Frete
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if (empty($fretes)): ?>

                <div class="alert alert-info">
                    Nenhum frete cadastrado.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Transportador</th>
                                <th>Produtos</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($fretes as $frete): ?>
                            <tr>
                                <td><?= (int)$frete['id'] ?></td>

                                <td><?= htmlspecialchars($frete['data'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= htmlspecialchars($frete['origem'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= htmlspecialchars($frete['destino'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= htmlspecialchars($frete['transportador'], ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= htmlspecialchars($frete['produtos'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
