
<?php

try {
    require __DIR__ . '/database/conexao.php';

    $consulta = $pdo->query(
        'SELECT id, nome, peso_kg FROM produtos ORDER BY id'
    );

    $produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    http_response_code(500);
    exit('Não foi possível carregar os produtos. Confira se o MySQL está ligado.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Produtos - Gerenciador Logístico</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <?php require __DIR__ . '/menu.php'; ?>

    <main class="container mt-4">

        <!-- Título e botão de cadastro -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

            <h2 class="mb-0">Produtos</h2>

            <a href="cadastrar-produto.php" class="btn btn-primary">
                + Cadastrar Produto
            </a>

        </div>

        <!-- Cartão da listagem -->
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <?php if ($produtos === []): ?>

                    <div class="alert alert-info mb-0" role="alert">
                        Nenhum produto cadastrado.
                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Peso (kg)</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($produtos as $produto): ?>

                                    <tr>
                                        <td>
                                            <?= (int) $produto['id'] ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td>
                                            <?= number_format((float) $produto['peso_kg'], 2, ',', '.') ?>
                                        </td>

                                        <td>
                                            <a
                                                href="editar-produto.php?id=<?= (int) $produto['id'] ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >
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

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
