<?php

$APIurl = 'https://eu210.chat-api.com/instance219965/';
$token = '1krwdq4lagx0dj1p';

$requisicaocod = file_get_contents("php://input");
$requisicao = json_decode($requisicaocod, TRUE);

$texto = urlencode($requisicao["messages"][0]["body"]);


$minha = $requisicao["messages"][0]['fromMe'];

function encerra_aposta($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://automatips.com.br/api/Adm/cashout?betid='.$id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'authority: automatips.com.br',
        'sec-ch-ua: "Chromium";v="94", "Google Chrome";v="94", ";Not A Brand";v="99"',
        'accept: */*',
        'content-type: application/json',
        'x-requested-with: XMLHttpRequest',
        'sec-ch-ua-mobile: ?0',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'referer: https://automatips.com.br/v2/dashboardAdm.html',
        'accept-language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        'cookie: token="soMe6uEUlLUIi6aslS1v7ons5EHGbnTkUQDMl9inUveRfXSpIEgdsQqeKGvdF3a"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-10-31T02:40:16Z; io=bk6qwm8f6U90rdR8ACR5'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

function requisitar_apostas(){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://automatips.com.br/api/Bot/getBets?token=4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D&pendentes=sim&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A&_=1630162602502',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'authority: automatips.com.br',
    'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
    'accept: application/json, text/javascript, */*; q=0.01',
    'cache-control: no-cache',
    'x-requested-with: XMLHttpRequest',
    'sec-ch-ua-mobile: ?0',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
    'sec-fetch-site: same-origin',
    'sec-fetch-mode: cors',
    'sec-fetch-dest: empty',
    'referer: https://automatips.com.br/v2/dashboardAdm.html',
    'accept-language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'cookie: token="4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-08-29T02:40:16Z'
  ),
));

    $response = json_decode(curl_exec($curl), TRUE);

    curl_close($curl);
    $ultimas_apostas = array_slice($response['Data'], 0, 29);
    return $ultimas_apostas;
}

function verifica_usuarios($id){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://automatips.com.br/api/Bot/getBetsUser?token=4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D&matchid='.$id.'&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A&_=1630162602507',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'authority: automatips.com.br',
    'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
    'accept: application/json, text/javascript, */*; q=0.01',
    'cache-control: no-cache',
    'x-requested-with: XMLHttpRequest',
    'sec-ch-ua-mobile: ?0',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
    'sec-fetch-site: same-origin',
    'sec-fetch-mode: cors',
    'sec-fetch-dest: empty',
    'referer: https://automatips.com.br/v2/dashboardAdm.html',
    'accept-language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'cookie: token="4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-08-29T02:40:16Z'
  ),
));

    $response = json_decode(curl_exec($curl), TRUE);

    curl_close($curl);

    return $response['Data'];
}

function verifica_status(){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://automatips.com.br/api/Adm/getUsuarios?token=4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'authority: automatips.com.br',
    'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
    'accept: application/json, text/javascript, */*; q=0.01',
    'x-requested-with: XMLHttpRequest',
    'sec-ch-ua-mobile: ?0',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
    'content-type: application/json; charset=utf-8',
    'sec-fetch-site: same-origin',
    'sec-fetch-mode: cors',
    'sec-fetch-dest: empty',
    'referer: https://automatips.com.br/v2/dashboardAdm.html',
    'accept-language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'cookie: token="4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-08-29T02:40:16Z'
  ),
));

$response = json_decode(curl_exec($curl), TRUE);

curl_close($curl);
return $response['Data'];
}

function muda_usuario($usuario, $status){
    $curl = curl_init();
    $array_usuarios = array("contarfxinvesting02@gmail.com" => array("02",
                                                                    "joycehenrique",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting03@gmail.com" => array("03",
                                                                    "gme777",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting05@gmail.com" => array("05",
                                                                    "brunnaqz",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting06@gmail.com" => array("06",
                                                                    "laianesouza1",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting09@gmail.com" => array("09",
                                                                    "taavinho223",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting11@gmail.com" => array("11",
                                                                    "jessicascfc",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting14@gmail.com" => array("14",
                                                                    "alyssonslv",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting18@gmail.com" => array("18",
                                                                    "dantasx10",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting30@gmail.com" => array("30",
                                                                    "amandahqcm",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting31@gmail.com" => array("31",
                                                                    "socorrobe",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting33@gmail.com" => array("33",
                                                                    "diogosn20",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting34@gmail.com" => array("34",
                                                                    "davi798",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting36@gmail.com" => array("36",
                                                                    "felipepamonha",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting37@gmail.com" => array("37",
                                                                    "joaonobre22",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting38@gmail.com" => array("38",
                                                                    "evertongbabet",
                                                                    "",
                                                                    ""),
                            "contarfxinvesting40@gmail.com" => array("40",
                                                                    "marconejs74",
                                                                    "",
                                                                    ""));
    curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://automatips.com.br/api/Usuario/alteraStatusClientePainel?email='.$usuario.'&contaBet365='.$array_usuarios[$usuario][1].'&status='.$status.'&token=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'authority: automatips.com.br',
    'sec-ch-ua: "Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
    'accept: application/json, text/javascript, */*; q=0.01',
    'x-requested-with: XMLHttpRequest',
    'sec-ch-ua-mobile: ?0',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
    'content-type: application/json; charset=utf-8',
    'sec-fetch-site: same-origin',
    'sec-fetch-mode: cors',
    'sec-fetch-dest: empty',
    'referer: https://automatips.com.br/v2/dashboardAdm.html',
    'accept-language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'cookie: token="4vP1qyA6QAb12tW34kSgVp01fjYm4cs8POlt5M75n13VmcXBcLcX3r50hySq5D"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-08-29T02:40:16Z'
  ),
));

    $response = json_decode(curl_exec($curl), TRUE);

    curl_close($curl);
    return $array_usuarios;
}

function seleciona_id_aposta($numero){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $seleciona_id = "SELECT id FROM aposta WHERE numero='$numero'";
    $result = pg_query($db_handle, $seleciona_id);
    $row = pg_fetch_assoc($result);
    $id = $row['id'];
    return $id;
}

function cadastra_apostas($apostas){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);

    $i = 1;
    foreach($apostas as $aposta){
        if($aposta['tipsterAtivo'] == 'jose alberto'){
        $id = $aposta['matchID'];
        $id2 = json_decode($aposta['dadosAposta'], TRUE)['betId'];
        $adicionar_query = "INSERT INTO aposta (numero, id, id2) VALUES ('$i', '$id', '$id2')";
        $adicionar_dados = pg_query($db_handle, $adicionar_query);
        $i++;
        }
    }
}

function seleciona_id2($numero){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $seleciona_id2 = "SELECT id2 FROM aposta WHERE numero='$numero'";
    $result = pg_query($db_handle, $seleciona_id2);
    $row = pg_fetch_assoc($result);
    $id2 = $row['id2'];
    return $id2;
}

$db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
$conversa_query = "SELECT * FROM chat WHERE numero=1";
$seleciona_conversa = pg_query($db_handle, $conversa_query);
$array_conversa = pg_fetch_array($seleciona_conversa, 0);

if(!empty($texto) and empty($array_conversa['menu'])){
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Selecione a opção desejada:*\n\n*1.* Reenviar apostas\n*2.* Religar todas as contas\n*3.* Encerrar Aposta"));
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $menu = 1;
    $hora = time();
    $menu_query = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $seleciona_menu = pg_query($db_handle, $menu_query);
}else if($texto == "1" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*Digite o número de alguma aposta para desligar as contas:*\n\n");
    $apostas = requisitar_apostas();
    $i = 1;
    foreach($apostas as $aposta){
        if($aposta['tipsterAtivo'] == 'jose alberto'){
        $mensagem = $mensagem.urlencode("*".$i.".* ".$aposta['evento']." - ".$aposta['aposta']."\n");
        $i++;
        }
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    cadastra_apostas($apostas);
    $hora = time();
    $menu = 2;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if($texto == "3" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*Digite o número de alguma aposta para encerrar:*\n\n");
    $apostas = requisitar_apostas();
    $i = 1;
    foreach($apostas as $aposta){
        if($aposta['tipsterAtivo'] == 'jose alberto'){
        $mensagem = $mensagem.urlencode("*".$i.".* ".$aposta['evento']." - ".$aposta['aposta']."\n");
        $i++;
        }
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    cadastra_apostas($apostas);
    $hora = time();
    $menu = 3;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if(is_numeric($texto) and $array_conversa['menu'] == 3 and ($array_conversa['hora'] + 1800) >= time()){
    $id2 = seleciona_id2($texto);
    encerra_aposta($id2);
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Comando de encerrar enviado. Confira no painel.*"));
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}else if(is_numeric($texto) and $array_conversa['menu'] == 2 and ($array_conversa['hora'] + 1800) >= time()){
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Desligando contas. Aguarde...*"));
    $id = seleciona_id_aposta($texto);
    $usuarios = verifica_usuarios($id);
    $email_usuarios_pegaram = array();
    $mensagem = urlencode("*Status dos Usuários:*\n\n");
    foreach($usuarios as $usuario){
        if($usuario['resultado'] == 0){
            $email_usuarios_pegaram[] = $usuario['emailUsuario'];
            $array_usuarios = muda_usuario($usuario['emailUsuario'], 0);
        }
    }
    $status = verifica_status();

    foreach($status as $user){
        if(!empty($array_usuarios[$user['email']][0])){
        if($user['statusPainel'] == 0){
            $array_usuarios[$user['email']][2] = "⚫";
        }else{
            $array_usuarios[$user['email']][2] = "🟢";
        }
        }
    }
    foreach($array_usuarios as $usuario){
        $mensagem = $mensagem.urlencode($usuario[0]." - ".$usuario[1]."  ".$usuario[2]."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}else if($texto == "2" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800)>= time()){
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Religando contas. Aguarde...*"));
    $usuarios = verifica_status();
    foreach($usuarios as $usuario){
        $array_usuarios = muda_usuario($usuario['email'], 1);
    }
    $status = verifica_status();
    $mensagem = urlencode("*Usuários ligados:*\n\n");
    foreach($status as $user){
        if(!empty($array_usuarios[$user['email']][0])){
        if($user['statusPainel'] == 0){
            $array_usuarios[$user['email']][2] = "⚫";
        }else{
            $array_usuarios[$user['email']][2] = "🟢";
        }
        }
    }
    foreach($array_usuarios as $usuario){
        $mensagem = $mensagem.urlencode($usuario[0]." - ".$usuario[1]."  ".$usuario[2]."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}else{
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Selecione a opção desejada:*\n\n*1.* Reenviar apostas\n*2.* Religar todas as contas\n*3.* Encerrar Aposta"));
    $menu = 1;
    $hora = time();
    $menu_query = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $seleciona_menu = pg_query($db_handle, $menu_query);
}

?>
