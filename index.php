<?php

require '../Slim/Slim/Slim.php';
require_once 'medoo.php';

\Slim\Slim::registerAutoloader();

$database = new medoo([
    // required
    'database_type' => 'mysql',
    'database_name' => 'db_busaobv',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
 
    // optional
    'port' => 3306,
    // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
    'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);
$app = new \Slim\Slim();

$app->response()->header('Content-Type', 'application/json;charset=utf-8');

$app->get('/', function () {
echo "Api BusãoBV \n";
});

$app->get('/linhas', function () {
echo "Método para obter todas as linhas do banco de dados.";
});

$app->post('/linhas', function() use ($database) {postLinhas($database);});

$app->post('/ruas', function() use ($database) {postRuas($database);});

$app->run();


function postLinhas ($database) {
    $SBAIRRO = 1;
    $SCENTRO = 2;
    $DUTIL   = 1;
    $SABADO  = 2;
    $DOMINGO = 3;
    $result = array();

    $request = \Slim\Slim::getInstance()->request();
    $linhas = json_decode($request->getBody(), true);

    foreach ($linhas as $linha) {
        $last_linha_id = $database->insert("tb_linha", ["linha_numero" => $linha['numero'],
                                                        "linha_nome"   => $linha['nome']]);

        $last_rota_id = $database->insert("tb_rota",   ["linha_id" => $last_linha_id]);

        /*
        * Na inserção dos horários é feito um foreach para percorrer cada array de horário
        * e dentro de cada iteração é inserido cada horário em um array, com seu tipo* e dia*,
        * para uma posterior inserção no banco de dados.
        *
        * TIPO: Determina se o horário é de SAÌDA DO BAIRRO [1] ou SAIDA DO CENTRO [2].
        * DIA: Determina se é um horário de DIAS ÚTEIS [1], SABADOS [2] ou DOMINGOS [3].
        */
        $horarios = array();
        foreach ($linha["horariosSaidaBairroUtil"] as $hora) {
            $horarios[] = ["horario_hora" => $hora,
                           "horario_tipo" => $SBAIRRO,
                           "horario_dia"  => $DUTIL,
                           "rota_id"      => $last_rota_id];

        }

        foreach ($linha["horariosSaidaCentroUtil"] as $hora) {
            $horarios[] = ["horario_hora" => $hora,
                           "horario_tipo" => $SCENTRO,
                           "horario_dia"  => $DUTIL,
                           "rota_id"      => $last_rota_id];
        }

        foreach ($linha["horariosSaidaBairroSabado"] as $hora) {
           $horarios[] = ["horario_hora" => $hora,
                          "horario_tipo" => $SBAIRRO,
                          "horario_dia"  => $SABADO,
                          "rota_id"      => $last_rota_id];
        }

        foreach ($linha["horariosSaidaCentroSabado"] as $hora) {
            $horarios[] = ["horario_hora" => $hora,
                           "horario_tipo" => $SCENTRO,
                           "horario_dia"  => $SABADO,
                           "rota_id"      => $last_rota_id];
        }

        foreach ($linha["horariosSaidaBairroDomingo"] as $hora) {
            $horarios[] = ["horario_hora" => $hora,
                           "horario_tipo" => $SBAIRRO,
                           "horario_dia"  => $DOMINGO,
                           "rota_id"      => $last_rota_id];
        }

        foreach ($linha["horariosSaidaCentroDomingo"] as $hora) {
            $horarios[] = ["horario_hora" => $hora,
                           "horario_tipo" => $SCENTRO,
                           "horario_dia"  => $DOMINGO,
                           "rota_id"      => $last_rota_id];
        }

        $last = $database->insert("tb_horario", $horarios);
        $result[] = ["horarios_inseridos" => count($last)];
        /*
        * Na inserção dos itinerários é feito um foreach para percorrer cada array de ruas
        * e dentro de cada iteração é inserido cada rua em um array com seu tipo* e ordem*,
        * além de fazer um SELECT para pegar o ID da rua que está sendo passada.
        *
        * O SELECT acontece pois as ruas foram previamente cadastradas, e o que se está registrando são,
        * seus respectivos ID's na ordem e tipo pretendida de cada rota.
        *
        * TIPO: Determina se o ititnerário é de SAIDA DO BAIRRO [1] ou SAIDA DO CENTRO [2].
        * ORDEM: Determina a ordem de cada rua dentro do itinerário.
        */
        $ruas = array();
        $ordem = 0;
        foreach ($linha["itinerarioSaidaBairro"] as $rua) {
            $rua_id_select = $database->get("tb_rua", "rua_id", ["rua_nome" => $rua]);

            $ruas[] = ["rota_id"        => $last_rota_id,
                       "rua_id"         => $rua_id_select,
                       "rota_rua_tipo"  => $SBAIRRO,
                       "rota_rua_ordem" => $ordem++];
        }

        $ordem = 0;
        foreach ($linha["itinerarioSaidaCentro"] as $rua) {
            $rua_id_select = $database->get("tb_rua", "rua_id", ["rua_nome" => $rua]);

            $ruas[] = ["rota_id"        => $last_rota_id,
                       "rua_id"         => $rua_id_select,
                       "rota_rua_tipo"  => $SCENTRO,
                       "rota_rua_ordem" => $ordem++];
        }

        $last = $database->insert("tb_rota_rua", $ruas);
        $result[] = ["ruas_inseridas" => count($last)];
    }

    echo json_encode($result);
}

function postRuas ($database){
    $request = \Slim\Slim::getInstance()->request();
    $ruasJSON = json_decode($request->getBody(), true);

    $ruas = array();
    foreach($ruasJSON as $rua){
        $ruas[] = ["rua_nome" => $rua];
    }

    $last_rua_insert = $database->insert("tb_rua", $ruas);

    echo(json_encode(["id_ruas_inseridos" => $last_rua_insert]));
}
