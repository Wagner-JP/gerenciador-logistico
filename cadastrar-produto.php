
<?php

$nome = '';
$descricao = '';
$peso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $peso = str_replace(',', '.', trim($_POST['peso_kg'] ?? ''));

    if ($nome === '' || mb_strlen($nome) > 120) {
        $erro = 'Informe um nome de até 120 caracteres.';
    } elseif (mb_strlen($descricao) > 255) {
        $erro = 'A descrição deve ter até 255 caracteres.';
    } elseif (
        !preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $peso)
        || (float) $peso <= 0
    ) {
        $erro = 'Informe um peso maior que zero, com até duas casas decimais.';
    } else {

        try {
            require __DIR__ . '/database/conexao.php';

            $comando = $pdo->prepare(
                'INSERT INTO produtos (nome, descricao, peso_kg)
                 VALUES (?, ?, ?)'
            );

            $comando->execute([$nome, $descricao, $peso]);

            header('Location: index.php', true, 303);
            exit;

        } catch (PDOException $erroBanco) {
            $erro = 'Não foi possível salvar o produto. Confira se o MySQL está ligado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Produto - Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cadastrar Produto</h2>

        <a href="index.php" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <?php if ($erro !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="cadastrar-produto.php">

                <div class="mb-3">
                    <label for="nome" class="form-label">
                        Nome do Produto
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        maxlength="120"
                        value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">
                        Descrição
                    </label>

                    <textarea
                        class="form-control"
                        id="descricao"
                        name="descricao"
                        maxlength="255"
                        rows="3"
                    ><?= htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="peso_kg" class="form-label">
                        Peso (kg)
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="peso_kg"
                        name="peso_kg"
                        min="0.01"
                        max="99999999.99"
                        step="0.01"
                        value="<?= htmlspecialchars($peso, ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >

                    <div class="form-text">
                        Informe o peso em quilogramas, com até duas casas decimais.
                    </div>
                </div>

                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        Salvar Produto
                    </button>

                    <a href="index.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
