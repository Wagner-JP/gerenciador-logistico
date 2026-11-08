
<?php
require __DIR__ . '/database/conexao.php';

$enderecos = $pdo->query(
    'SELECT id, identificacao FROM enderecos ORDER BY identificacao'
)->fetchAll(PDO::FETCH_KEY_PAIR);

$transportadores = $pdo->query(
    'SELECT id, nome FROM transportadores ORDER BY nome'
)->fetchAll(PDO::FETCH_KEY_PAIR);

$produtos = $pdo->query(
    'SELECT id, nome FROM produtos ORDER BY nome'
)->fetchAll(PDO::FETCH_KEY_PAIR);

$campos = [
    'origem_id' => ['Origem', $enderecos],
    'destino_id' => ['Destino', $enderecos],
    'transportador_id' => ['Transportador', $transportadores]
];

$selecionados = [];
$data = '';
$itens = [''];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dataRecebida = $_POST['data_frete'] ?? '';
    $data = is_string($dataRecebida) ? trim($dataRecebida) : '';

    foreach ($campos as $chave => [$rotulo, $opcoes]) {
        $id = filter_input(INPUT_POST, $chave, FILTER_VALIDATE_INT);
        $selecionados[$chave] = $id ?: 0;

        if (!isset($opcoes[$selecionados[$chave]])) {
            $erro = 'Selecione uma origem, um destino e um transportador válidos.';
        }
    }

    $itensRecebidos = $_POST['produtos'] ?? [];
    $itens = is_array($itensRecebidos) ? $itensRecebidos : [];

    $dataValida = DateTimeImmutable::createFromFormat('!Y-m-d', $data);

    if ($erro === '') {
        if (!$dataValida || $dataValida->format('Y-m-d') !== $data) {
            $erro = 'Informe uma data válida.';
        } elseif ($selecionados['origem_id'] === $selecionados['destino_id']) {
            $erro = 'Escolha endereços diferentes para origem e destino.';
        } elseif ($itens === []) {
            $erro = 'Selecione pelo menos um produto.';
        } else {
            $idsValidos = [];

            foreach ($itens as $produtoId) {
                if (
                    !is_scalar($produtoId) ||
                    !ctype_digit((string) $produtoId) ||
                    !isset($produtos[(int) $produtoId])
                ) {
                    $erro = 'Selecione produtos válidos e preencha todas as linhas.';
                    break;
                }

                $idsValidos[] = (int) $produtoId;
            }

            if (
                $erro === '' &&
                count($idsValidos) !== count(array_unique($idsValidos))
            ) {
                $erro = 'Selecione cada produto apenas uma vez.';
            }
        }
    }

    if ($erro === '') {
        try {
            $pdo->beginTransaction();

            $frete = $pdo->prepare(
                'INSERT INTO fretes
                (data_frete, origem_id, destino_id, transportador_id)
                VALUES (?, ?, ?, ?)'
            );

            $frete->execute([
                $data,
                $selecionados['origem_id'],
                $selecionados['destino_id'],
                $selecionados['transportador_id']
            ]);

            $freteId = (int) $pdo->lastInsertId();

            $itensFrete = $pdo->prepare(
                'INSERT INTO frete_produtos
                (frete_id, produto_id) VALUES (?, ?)'
            );

            foreach ($idsValidos as $produtoId) {
                $itensFrete->execute([$freteId, $produtoId]);
            }

            $pdo->commit();

            header('Location: novo-frete.php?salvo=' . $freteId, true, 303);
            exit;

        } catch (PDOException $erroBanco) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $erro = 'Não foi possível salvar o frete. Tente novamente.';
        }
    }
}

$salvo = filter_input(INPUT_GET, 'salvo', FILTER_VALIDATE_INT);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Novo Frete - Gerenciador Logístico</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<main class="container mt-4 pb-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Novo Frete</h2>

        <a href="fretes.php" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <?php if ($erro !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php elseif ($salvo): ?>
                <div class="alert alert-success" role="status">
                    Frete nº <?= (int) $salvo ?> cadastrado com sucesso!
                </div>
            <?php endif; ?>

            <form method="post" action="novo-frete.php">

                <div class="mb-3">
                    <label for="data_frete" class="form-label">
                        Data do Frete
                    </label>

                    <input
                        type="date"
                        class="form-control"
                        id="data_frete"
                        name="data_frete"
                        required
                        value="<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <?php foreach ($campos as $chave => [$rotulo, $opcoes]): ?>
                    <div class="mb-3">
                        <label for="<?= $chave ?>" class="form-label">
                            <?= htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8') ?>
                        </label>

                        <select
                            class="form-select"
                            id="<?= $chave ?>"
                            name="<?= $chave ?>"
                            required
                        >
                            <option value="">Selecione...</option>

                            <?php foreach ($opcoes as $id => $nome): ?>
                                <option
                                    value="<?= (int) $id ?>"
                                    <?= ($selecionados[$chave] ?? 0) === (int) $id ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>

                <hr class="my-4">

                <h4 class="mb-3">Produtos do Frete</h4>

                <div id="itens">

                    <?php foreach ($itens ?: [''] as $produtoSelecionado): ?>
                        <div class="produto-linha row g-2 align-items-end mb-3">

                            <div class="col-md-10">
                                <label class="form-label">
                                    Produto
                                </label>

                                <select
                                    name="produtos[]"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Selecione...</option>

                                    <?php foreach ($produtos as $id => $nome): ?>
                                        <option
                                            value="<?= (int) $id ?>"
                                            <?= (int) $produtoSelecionado === (int) $id ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger w-100 remover-produto"
                                >
                                    Remover
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>

                <button
                    type="button"
                    id="adicionar"
                    class="btn btn-outline-primary mb-4"
                >
                    + Adicionar Produto
                </button>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        Salvar Frete
                    </button>

                    <a href="fretes.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>

            </form>

        </div>
    </div>

</main>

<script>
const itens = document.getElementById('itens');
const adicionar = document.getElementById('adicionar');

adicionar.addEventListener('click', () => {
    const primeiraLinha = itens.querySelector('.produto-linha');
    const novaLinha = primeiraLinha.cloneNode(true);
    const seletor = novaLinha.querySelector('select');

    seletor.value = '';
    itens.appendChild(novaLinha);
    seletor.focus();
});

itens.addEventListener('click', (evento) => {
    if (!evento.target.classList.contains('remover-produto')) {
        return;
    }

    const linhas = itens.querySelectorAll('.produto-linha');

    if (linhas.length > 1) {
        evento.target.closest('.produto-linha').remove();
    } else {
        alert('O frete precisa ter pelo menos um produto.');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
