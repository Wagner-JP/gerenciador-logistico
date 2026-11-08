
<?php

try {
    require __DIR__ . '/database/conexao.php';

    $consulta = $pdo->query(
        'SELECT id, identificacao, cidade, uf FROM enderecos ORDER BY id'
    );

    $enderecos = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    http_response_code(500);
    exit('Não foi possível carregar os endereços.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endereços - Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Endereços</h2>

        <a href="cadastrar-endereco.php" class="btn btn-primary">
            + Cadastrar Endereço
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if ($enderecos === []): ?>

                <div class="alert alert-info mb-0">
                    Nenhum endereço cadastrado.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Identificação</th>
                                <th>Cidade / UF</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($enderecos as $endereco): ?>
                                <tr>
                                    <td><?= (int) $endereco['id'] ?></td>

                                    <td>
                                        <?= htmlspecialchars($endereco['identificacao'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($endereco['cidade'] . ' / ' . $endereco['uf'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td>
                                        <a href="editar-endereco.php?id=<?= (int) $endereco['id'] ?>"
                                           class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
