
<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=gerenciador_logistico;charset=utf8mb4',
        'SEU_USUARIO',
        'SUA_SENHA',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $erro) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados.');
}
