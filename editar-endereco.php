
<?php

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if (!$id) {
    http_response_code(400);
    exit('ID de endereço inválido.');
}

$campos = [
    'identificacao' => ['Identificação', 120],
    'rua' => ['Rua', 150],
    'numero' => ['Número', 20],
    'cep' => ['CEP', 9],
    'cidade' => ['Cidade', 100],
    'uf' => ['UF', 2],
];

$erro = '';

try {
    require __DIR__ . '/database/conexao.php';

    $consulta = $pdo->prepare(
        'SELECT identificacao, rua, numero, cep, cidade, uf
         FROM enderecos WHERE id = ?'
    );

    $consulta->execute([$id]);
    $dados = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        http_response_code(404);
        exit('Endereço não encontrado.');
    }

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

            $atualizar = $pdo->prepare(
                'UPDATE enderecos
                 SET identificacao = :identificacao,
                     rua = :rua,
                     numero = :numero,
                     cep = :cep,
                     cidade = :cidade,
                     uf = :uf
                 WHERE id = :id'
            );

            $atualizar->execute($dados + ['id' => $id]);

            header('Location: enderecos.php', true, 303);
            exit;
        }
    }

} catch (PDOException $erroBanco) {
    http_response_code(500);
    exit('Não foi possível carregar ou atualizar o endereço.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Endereço - Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar Endereço</h2>

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
            <?php endif; ?>

            <form method="post" action="editar-endereco.php?id=<?= (int) $id ?>">

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
                        Salvar Alterações
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
