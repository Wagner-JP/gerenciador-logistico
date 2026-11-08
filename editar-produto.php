
<?php

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if (!$id) {
    http_response_code(400);
    exit('ID de produto inválido.');
}

$erro = '';

try {
    require __DIR__ . '/database/conexao.php';

    $consulta = $pdo->prepare(
        'SELECT nome, descricao, peso_kg FROM produtos WHERE id = ?'
    );

    $consulta->execute([$id]);
    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        http_response_code(404);
        exit('Produto não encontrado.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $peso = $_POST['peso_kg'] ?? '';

        $produto['nome'] = is_string($nome) ? trim($nome) : '';
        $produto['descricao'] = is_string($descricao) ? trim($descricao) : '';
        $produto['peso_kg'] = is_string($peso)
            ? str_replace(',', '.', trim($peso))
            : '';

        if (
            $produto['nome'] === '' ||
            mb_strlen($produto['nome']) > 120
        ) {
            $erro = 'Informe um nome de até 120 caracteres.';

        } elseif (mb_strlen($produto['descricao']) > 255) {
            $erro = 'A descrição deve ter até 255 caracteres.';

        } elseif (
            !preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $produto['peso_kg'])
            || (float) $produto['peso_kg'] <= 0
        ) {
            $erro = 'Informe um peso maior que zero, com até duas casas decimais.';

        } else {

            $atualizar = $pdo->prepare(
                'UPDATE produtos
                 SET nome = ?, descricao = ?, peso_kg = ?
                 WHERE id = ?'
            );

            $atualizar->execute([
                $produto['nome'],
                $produto['descricao'],
                $produto['peso_kg'],
                $id
            ]);

            header('Location: index.php', true, 303);
            exit;
        }
    }

} catch (PDOException $erroBanco) {
    http_response_code(500);
    exit('Não foi possível carregar ou atualizar o produto.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Produto - Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar Produto</h2>

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

            <form method="post" action="editar-produto.php?id=<?= (int) $id ?>">

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
                        required
                        value="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>"
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
                    ><?= htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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
                        required
                        value="<?= htmlspecialchars(str_replace(',', '.', (string) $produto['peso_kg']), ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div class="form-text">
                        Informe o peso em quilogramas, com até duas casas decimais.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Salvar Alterações
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
