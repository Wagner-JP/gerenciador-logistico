
<?php

$nome = '';
$telefone = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim(filter_input(INPUT_POST, 'nome') ?? '');
    $telefone = trim(filter_input(INPUT_POST, 'telefone') ?? '');

    if ($nome === '' || mb_strlen($nome) > 120) {
        $erro = 'Informe um nome ou razão social de até 120 caracteres.';

    } elseif (
        mb_strlen($telefone) > 20 ||
        !preg_match('/^\(\d{2}\) \d{4,5}-\d{4}$/D', $telefone)
    ) {
        $erro = 'Informe o telefone no formato (45) 0000-0000 ou (45) 90000-0000.';

    } else {
        try {
            require __DIR__ . '/database/conexao.php';

            $comando = $pdo->prepare(
                'INSERT INTO transportadores (nome, telefone) VALUES (?, ?)'
            );

            $comando->execute([$nome, $telefone]);

            header('Location: cadastrar-transportador.php?salvo=1', true, 303);
            exit;

        } catch (PDOException $erroBanco) {
            $erro = 'Não foi possível salvar o transportador. Confira se o banco está ligado e se a tabela existe.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Transportador - Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cadastrar Transportador</h2>

        <a href="transportadores.php" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <?php if ($erro !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php elseif (($_GET['salvo'] ?? '') === '1'): ?>
                <div class="alert alert-success" role="status">
                    Transportador cadastrado com sucesso!
                </div>
            <?php endif; ?>

            <form method="post" action="cadastrar-transportador.php">

                <div class="mb-3">
                    <label for="nome" class="form-label">
                        Nome ou Razão Social
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <div class="mb-3">
                    <label for="telefone" class="form-label">
                        Telefone
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        id="telefone"
                        name="telefone"
                        maxlength="20"
                        placeholder="(45) 90000-0000"
                        required
                        value="<?= htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="form-text">
                        Informe o DDD e o telefone com espaços e hífen.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Salvar Transportador
                    </button>

                    <a href="transportadores.php" class="btn btn-outline-secondary">
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
