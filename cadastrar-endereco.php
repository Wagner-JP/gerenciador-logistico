
<?php

$campos = [
    'identificacao' => ['Identificação', 120],
    'rua' => ['Rua', 150],
    'numero' => ['Número', 20],
    'cep' => ['CEP', 9],
    'cidade' => ['Cidade', 100],
    'uf' => ['UF', 2],
];

$dados = array_fill_keys(array_keys($campos), '');
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($campos as $chave => [$rotulo, $limite]) {
        $valor = $_POST[$chave] ?? '';
        $dados[$chave] = is_string($valor) ? trim($valor) : '';
    }

    $dados['uf'] = strtoupper($dados['uf']);

    foreach ($campos as $chave => [$rotulo, $limite]) {
        if ($dados[$chave] === '' || mb_strlen($dados[$chave]) > $limite) {
            $erro = "Preencha o campo $rotulo com até $limite caracteres.";
            break;
        }
    }

    if ($erro === '' && !preg_match('/^\d{5}-\d{3}$/D', $dados['cep'])) {
        $erro = 'Informe o CEP no formato 00000-000.';
    }

    if ($erro === '' && !preg_match('/^[A-Z]{2}$/D', $dados['uf'])) {
        $erro = 'Informe a UF com duas letras, como PR.';
    }

    if ($erro === '') {
        try {
            require __DIR__ . '/database/conexao.php';

            $comando = $pdo->prepare(
                'INSERT INTO enderecos (identificacao, rua, numero, cep, cidade, uf)
                 VALUES (:identificacao, :rua, :numero, :cep, :cidade, :uf)'
            );

            $comando->execute($dados);

            header('Location: cadastrar-endereco.php?salvo=1', true, 303);
            exit;

        } catch (PDOException $erroBanco) {
            $erro = 'Não foi possível salvar o endereço. Confira a conexão com o banco.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Endereço - Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cadastrar Endereço</h2>

        <a href="enderecos.php" class="btn btn-secondary">
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
                    Endereço cadastrado com sucesso!
                </div>
            <?php endif; ?>

            <form method="post" action="cadastrar-endereco.php">

                <?php foreach ($campos as $chave => [$rotulo, $limite]): ?>

                    <div class="mb-3">

                        <label for="<?= $chave ?>" class="form-label">
                            <?= htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8') ?>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="<?= $chave ?>"
                            name="<?= $chave ?>"
                            maxlength="<?= (int) $limite ?>"
                            value="<?= htmlspecialchars($dados[$chave], ENT_QUOTES, 'UTF-8') ?>"
                            <?= $chave === 'cep' ? 'placeholder="00000-000" inputmode="numeric"' : '' ?>
                            <?= $chave === 'uf' ? 'placeholder="PR"' : '' ?>
                            required
                        >

                    </div>

                <?php endforeach; ?>

                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        Salvar Endereço
                    </button>

                    <a href="enderecos.php" class="btn btn-outline-secondary">
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
