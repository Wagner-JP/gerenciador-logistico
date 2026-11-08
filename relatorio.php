
<?php

$data = filter_input(INPUT_GET, 'data') ?: '';
$erro = '';

if (isset($_GET['data'])) {

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $data)) {
        $erro = 'Selecione uma data válida.';
    } else {
        [$ano, $mes, $dia] = explode('-', $data);

        if (!checkdate((int) $mes, (int) $dia, (int) $ano)) {
            $erro = 'Selecione uma data válida.';
        }
    }

    if ($erro === '') {

        try {
            require __DIR__ . '/database/conexao.php';

            $sql = "
                SELECT f.id,
                       DATE_FORMAT(f.data_frete, '%d/%m/%Y') AS data_formatada,
                       origem.identificacao AS origem,
                       destino.identificacao AS destino,
                       t.nome AS transportador,
                       p.nome AS produto
                FROM fretes AS f
                JOIN enderecos AS origem ON origem.id = f.origem_id
                JOIN enderecos AS destino ON destino.id = f.destino_id
                JOIN transportadores AS t ON t.id = f.transportador_id
                LEFT JOIN frete_produtos AS fp ON fp.frete_id = f.id
                LEFT JOIN produtos AS p ON p.id = fp.produto_id
                WHERE f.data_frete = ?
                ORDER BY f.id, p.nome
            ";

            $consulta = $pdo->prepare($sql);
            $consulta->execute([$data]);
            $linhas = $consulta->fetchAll(PDO::FETCH_NUM);

            if ($linhas === []) {
                $erro = 'Nenhum frete encontrado para essa data.';
            } else {

                header('Content-Type: text/csv; charset=UTF-8');
                header(
                    'Content-Disposition: attachment; filename="fretes-' .
                    $data . '.csv"'
                );

                $arquivo = fopen('php://output', 'w');

                fwrite($arquivo, "\xEF\xBB\xBF");

                fputcsv(
                    $arquivo,
                    [
                        'ID do frete',
                        'Data',
                        'Origem',
                        'Destino',
                        'Transportador',
                        'Produto'
                    ],
                    ';',
                    '"',
                    ''
                );

                foreach ($linhas as $linha) {

                    foreach ($linha as &$valor) {
                        if (
                            is_string($valor) &&
                            preg_match('/^\s*[=+\-@]/u', $valor)
                        ) {
                            $valor = "'" . $valor;
                        }
                    }

                    unset($valor);

                    fputcsv($arquivo, $linha, ';', '"', '');
                }

                fclose($arquivo);
                exit;
            }

        } catch (PDOException $erroBanco) {
            $erro = 'Não foi possível gerar o relatório. Verifique a conexão com o banco de dados.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Relatório Diário - Gerenciador Logístico</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<?php require __DIR__ . '/menu.php'; ?>

<main class="container mt-4 pb-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <h2 class="mb-0">Relatório Diário</h2>

        <a href="fretes.php" class="btn btn-secondary">
            Voltar para Fretes
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body p-4">

            <h5 class="card-title mb-3">
                Exportar relatório de fretes
            </h5>

            <p class="text-muted">
                Selecione uma data para gerar um relatório
                com os fretes cadastrados nesse dia.
                O arquivo será baixado no formato CSV.
            </p>

            <?php if ($erro !== ''): ?>

                <div class="alert alert-warning" role="alert">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <form method="get" action="relatorio.php">

                <div class="mb-3">

                    <label for="data" class="form-label">
                        Data do Frete
                    </label>

                    <input
                        type="date"
                        id="data"
                        name="data"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?>"
                    >

                </div>

                <div class="d-flex flex-wrap gap-2">

                    <button type="submit" class="btn btn-primary">
                        Baixar Relatório CSV
                    </button>

                    <a href="fretes.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
