<?php

function seleciona_partidas_db($fem_masc){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $query = "DELETE FROM $fem_masc WHERE NOW() - session_time > '48 hours'::interval";
    $rs = pg_query($db_handle, $query);
    $query = "SELECT * FROM $fem_masc";
    $rs = pg_query($db_handle, $query);
    $row = pg_fetch_all($rs);
    $i = 1;
    foreach($row as $partida){
        $jogo = $partida['partida'];
        $menu_query = "UPDATE $fem_masc SET numero='$i' WHERE partida='$jogo'";
        $rs = pg_query($db_handle, $menu_query);
        $i++;
    }
    $query = "SELECT * FROM $fem_masc";
    $rs = pg_query($db_handle, $query);
    $row = pg_fetch_all($rs);

    return $row;
}

function marca_partida_repassar($fem_masc, $numero, $verd_falso){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $menu_query = "UPDATE $fem_masc SET repassar='$verd_falso' WHERE numero='$numero'";
    $rs = pg_query($db_handle, $menu_query);
    $row = seleciona_partida_cadastrada($fem_masc);
    $query = "UPDATE $fem_masc SET numero=NULL WHERE numero IS NOT NULL";
    $rs = pg_query($db_handle, $query);
    return $row;
}

function seleciona_partida_cadastrada($fem_masc){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $query = "SELECT * FROM $fem_masc WHERE repassar";
    $rs = pg_query($db_handle, $query);
    $row = pg_fetch_all($rs);
    $i = 1;
    foreach($row as $partida){
        $jogo = $partida['partida'];
        $menu_query = "UPDATE $fem_masc SET numero='$i' WHERE partida='$jogo'";
        $rs = pg_query($db_handle, $menu_query);
        $i++;
    }
    $query = "SELECT * FROM $fem_masc WHERE repassar";
    $rs = pg_query($db_handle, $query);
    $row = pg_fetch_all($rs);

    return $row;
}



function verifica_contas_encerradas($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://automatips.com.br/api/Adm/getLogAposta?token=soMe6uEUlLUIi6aslS1v7ons5EHGbnTkUQDMl9inUveRfXSpIEgdsQqeKGvdF3a&idAposta='.$id.'&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A&_=163501092167477',
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
        'accept: application/json, text/javascript, */*; q=0.01',
        'cache-control: no-cache',
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

    $response = json_decode(curl_exec($curl), TRUE)['Data'];

    curl_close($curl);

    $usuarios_novos = [];
    foreach($response as $linha){
        if($linha['logTexto'] == "Cashout realizado com sucesso!"){
            $usuarios_novos[] = $linha['contausuario'];
        }
    }
    $usuarios_novos = array_unique($usuarios_novos);

    return $usuarios_novos;
}

function verifica_usuario($id, $usuarios_antigos, $partida){
    $usuarios_novos = verifica_contas_encerradas($id);

    if(count($usuarios_novos) != count($usuarios_antigos)){
        envia_contas_encerradas($usuarios_novos, $partida);
    }
    return $usuarios_novos;
}

function envia_contas_encerradas($usuarios, $partida){
    $APIurl = getenv('API_URL');
    $token = getenv('TOKEN');

    $contas_novas = atualiza_contas();
    $array_usuarios = [];

    foreach($contas_novas as $conta){
        $array_usuarios[$conta['usuario']][0] = $conta['numero'];
        $array_usuarios[$conta['usuario']][1] = $conta['usuario'];
        $array_usuarios[$conta['usuario']][2] = "";
        $array_usuarios[$conta['usuario']][3] = " ???";
    }

    foreach($usuarios as $usuario){
            $array_usuarios[$usuario][3] = " ????";
    }

    $mensagem = urlencode("?????? *ENCERRANDO APOSTA*\n\n*".$partida."*\n\n");
    foreach($array_usuarios as $usuario){
        $mensagem = $mensagem.urlencode($usuario[0]." - ".$usuario[1].$usuario[3]."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623295183@g.us&body=".$mensagem);

}

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
$response = [];
for($i=1;$i<=2;$i++){
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://app.bcopy.com.br/tipster/getBets?page='.$i,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'authority: app.bcopy.com.br',
    'accept: application/json, text/javascript, */*; q=0.01',
    'accept-language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'cookie: XSRF-TOKEN=eyJpdiI6IlNYZ293bzBoZElGTHZPUEV6Yklzb3c9PSIsInZhbHVlIjoiV3M3VGE4RTRhMGdVZlRCeVFTL1FzNjVkQ29ubnJpSlAzZW5VZTJ4KzR3c0poNG50OUVocjYvWTgzSEp6ZWFsNUVHRHFJYzBzMGlVSzlKdnpGOEUyeXh0WGorNTFzR0hTMHhYWHIrQzRHYVUreHZkTGFRUlA5Q3VmQkFGOE1jUTEiLCJtYWMiOiI5ZTAxMDJkYjZmODE1OGZkZTU5MjRlMjhiZTA0ODFiYzhlODYyNGNmNmI2MmQ4MDRmNzE1NzBjNTM1ZDQyMTQ5IiwidGFnIjoiIn0%3D; bcopy_session=eyJpdiI6IjdraHJNQWxNS1Jrd1A4Vk1tWkFwV2c9PSIsInZhbHVlIjoieEUwYU5NMUZjNXNsOVQwanFEMmtUSjVlakpyVUM1YW9ja3VCclVIeSt6aVYzcUVuVXVIZGpNcWtmNFRSRzFNcXBHam9PWjRGK0NIVHJsZ0VhUk9qYmxBcm4zVSs4ZjRxZGVnckJ5YjNWcnNOdlpXeVREOCtOU25ZVm5GeWd6bUciLCJtYWMiOiJmY2U4YjI1MGI4MjBmZGI3NjYyY2JkZmIxZDEwZmIwYzU5ZDY3ZDYxOTI1MzFkNmU0Njk5NzRkMjg3Y2Y0ZTNkIiwidGFnIjoiIn0%3D; XSRF-TOKEN=eyJpdiI6ImZVa2NFWjFDK0lFOXU3cVU2cnBWSXc9PSIsInZhbHVlIjoiVUc2bTF0RktpdnlnTnpHTnFsbXVnNHdVa3V5Y3dkSkRQN3RWUnY1dVM2TzF1MXJBRVlDNldhNk5sakMvN2tTQ1NCbitKVXNhZTlPMFlKVnA1MkRzeElNeGw0bjRWczl6WkgrUGh1SnByelRlN0E5TVZHZmRoT2JaTkUrWlZiejkiLCJtYWMiOiJlZTg5ZGU4MWNiNzg5NWNiZDkwYWNlZTUyMTA4NDRmNDY3NDEyMzk2MTkwY2Q5YTg3M2UwM2Q2OTM3ZjYzM2M3IiwidGFnIjoiIn0%3D; bcopy_session=eyJpdiI6ImljeWM2SDY0c29VclFNYkF0M1U4ZHc9PSIsInZhbHVlIjoiZGYxRzNKZ0dieThaTm9IMGppclk2YU5HQ0k1NjY5aDgweTcxSVROZER6RFhhWCsxelRXa0VtdXptK0xvbG1PeER0Z1RwK1lxYkx0bXdsNFI4cUNsOGFyWHlhRzJSS2F1Yy9PNktMT3FzRnFyWjZ3eTFvcmlKbGxFRm1teTZqSm0iLCJtYWMiOiI3NGEyYTFhNzUyMWRmNzQyYjc0MWQwMzM4NTkyNGE4MTUyMDIzYTU3NjEyMTdlYWM3YmI4NmVlZjgzOGZjNmFhIiwidGFnIjoiIn0%3D',
    'referer: https://app.bcopy.com.br/tipster/dash',
    'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="100", "Google Chrome";v="100"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36',
    'x-requested-with: XMLHttpRequest'
  ),
));

$response[] = json_decode(curl_exec($curl), TRUE)['data'];

}
curl_close($curl);

$ultimas_apostas = [];
$array_multipla = array(2=>'Dupla', 3=>'Tripla');
$i=1;
foreach($response as $resposta){
    foreach($resposta as $aposta){
        $ultimas_apostas[$i]['id'] = $aposta['id'];
        $ultimas_apostas[$i]['id2'] = $aposta['unicId'];
        if($aposta['type'] == 'single'){
            $ultimas_apostas[$i]['partida'] = $aposta['bt'][0]['fd'];
            $ultimas_apostas[$i]['mercado'] = $aposta['bt'][0]['pt'][0]['md'];
        }else{
            $j = 0;
            $ultimas_apostas[$i]['partida'] = $aposta['bt'][0]['fd'];
            foreach($aposta['bt'] as $linha){
                if($j>0){
                    $ultimas_apostas[$i]['partida'] = $ultimas_apostas[$i]['partida'] . " + " . $aposta['bt'][0]['fd'];
                }
                $j++;
            }
            if(array_key_exists(sizeof($aposta['bt']), $array_multipla)){
                $ultimas_apostas[$i]['mercado'] = $array_multipla[sizeof($aposta['bt'])];
            }else{
                $ultimas_apostas[$i]['mercado'] = 'M??ltipla';
            }
        }
        $i++;
    }
}
    return $ultimas_apostas;
}

function verifica_usuarios($id){
    $curl = curl_init();


curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://automatips.com.br/api/Adm/getLogAposta?token=YBknWTkY6FUER0owiPffbMSucHbRvqFnSxgUR7TasBXEuW1YLqBda0wi2KgQO&idAposta='.$id.'&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A&_=1637321206634',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'authority: automatips.com.br',
    'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"',
    'accept: application/json, text/javascript, */*; q=0.01',
    'cache-control: no-cache',
    'x-requested-with: XMLHttpRequest',
    'sec-ch-ua-mobile: ?0',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-site: same-origin',
    'sec-fetch-mode: cors',
    'sec-fetch-dest: empty',
    'referer: https://automatips.com.br/v2/dashboardAdm.html',
    'accept-language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'cookie: token="YBknWTkY6FUER0owiPffbMSucHbRvqFnSxgUR7TasBXEuW1YLqBda0wi2KgQO"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-11-30T02:40:16Z'
  ),
));

    $response = json_decode(curl_exec($curl), TRUE)['Data'];

    $usuarios = array();

    foreach($response as $log){
        if($log['logTexto'] == "Aposta realizada com sucesso!"){
            $usuarios[] = $log['contausuario'];
        }
    }

    curl_close($curl);

    return $usuarios;
}

function verifica_status(){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://automatips.com.br/api/Adm/getUsuarios?token=soMe6uEUlLUIi6aslS1v7ons5EHGbnTkUQDMl9inUveRfXSpIEgdsQqeKGvdF3a&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A',
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
    'cookie: token="soMe6uEUlLUIi6aslS1v7ons5EHGbnTkUQDMl9inUveRfXSpIEgdsQqeKGvdF3a"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-08-29T02:40:16Z'
  ),
));

$response = json_decode(curl_exec($curl), TRUE);

curl_close($curl);
return $response['Data'];
}

function muda_usuario($usuario, $status){
    $curl = curl_init();

    $contas_novas = atualiza_contas();

    $array_usuarios = [];

    foreach($contas_novas as $conta){
        $array_usuarios[$conta['email']][0] = $conta['numero'];
        $array_usuarios[$conta['email']][1] = $conta['usuario'];
        $array_usuarios[$conta['email']][2] = "";
        $array_usuarios[$conta['email']][3] = " ???";
    }

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
    'cookie: token="soMe6uEUlLUIi6aslS1v7ons5EHGbnTkUQDMl9inUveRfXSpIEgdsQqeKGvdF3a"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-08-29T02:40:16Z'
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

function seleciona_partida_aposta($id){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $seleciona_partida = "SELECT partida FROM aposta WHERE id='$id'";
    $result = pg_query($db_handle, $seleciona_partida);
    $row = pg_fetch_assoc($result);
    $partida = $row['partida'];
    return $partida;
}

function cadastra_apostas($apostas){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);

    foreach($apostas as $numero => $aposta){
        $id = $aposta['matchID'];
        $id2 = json_decode($aposta['dadosAposta'], TRUE)['betId'];
        $partida = $aposta['partida']." - ".$aposta['mercado'];
        $adicionar_query = "INSERT INTO aposta (numero, id, id2, partida) VALUES ('$numero', '$id', '$id2', '$partida')";
        $adicionar_dados = pg_query($db_handle, $adicionar_query);
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

function seleciona_numeropartida(){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $seleciona_numeropartida = "SELECT numeropartida FROM chat WHERE menu='5'";
    $result = pg_query($db_handle, $seleciona_numeropartida);
    $row = pg_fetch_assoc($result);
    $numeropartida = $row['numeropartida'];
    return $numeropartida;
}
function pega_partidas_db($num_partidas){
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $query = "SELECT * FROM aposta LIMIT '$num_partidas'";
    $rs = pg_query($db_handle, $query);
    $row = pg_fetch_all($rs);
    return $row;
}
function verifica_apostas_concluidas($array_aposta){
    $array_aposta_cadastrada = array();
    $i = 0;
    foreach($array_aposta as $aposta){
        foreach($array_aposta as $key => $aposta_duplicada){
            if($aposta['partida'] == $aposta_duplicada['partida']){
                $array_aposta_cadastrada[$aposta['partida']][] = $aposta_duplicada;
                unset($array_aposta[$key]);
            }
        }
        $i++;
    }
    $mensagem = "";
    foreach($array_aposta_cadastrada as $key => $aposta){
        $contas_novas = atualiza_contas();

        $array_usuarios = [];

        foreach($contas_novas as $conta){
            $array_usuarios[$conta['usuario']][0] = $conta['numero'];
            $array_usuarios[$conta['usuario']][1] = $conta['usuario'];
            $array_usuarios[$conta['usuario']][2] = "0";
            $array_usuarios[$conta['usuario']][3] = " ???";
        }
        $usuarios_aposta = array();
        $controle_duplicadas = 0;
        $controle_naofeitas = 0;
        foreach($aposta as $aposta_duplicada){
            $usuarios = verifica_usuarios($aposta_duplicada['id']);
            foreach($usuarios as $usuario){
                    $array_usuarios[$usuario][2]++;
            }
        }
        
        $mensagem_duplicadas = "???? Contas duplicadas:\n";
        $mensagem_naofeitas = "??? Contas que n??o fizeram:\n";
        foreach($array_usuarios as $usuario){
            if($usuario[2] != 1){
                if($usuario[2] == 0){
                    $controle_naofeitas = 1;
                    $mensagem_naofeitas = $mensagem_naofeitas.$usuario[0]." - ".$usuario[1]."\n";
                }if($usuario[2] > 1){
                    $controle_duplicadas = 1;
                    $mensagem_duplicadas = $mensagem_duplicadas.$usuario[0]." - ".$usuario[1]." (".$usuario[2]."x)\n";
                }
            }
        }
        
        if($controle_naofeitas == 1 or $controle_duplicadas == 1){
            $mensagem = $mensagem."*".$key."*\n\n";
            if($controle_naofeitas == 1){
                $mensagem = "\n".$mensagem.$mensagem_naofeitas."\n";
            }if($controle_duplicadas == 1){
                $mensagem = "\n".$mensagem.$mensagem_duplicadas."\n";
            }
        }
        
    }
    return $mensagem;
}
function envia_dados($data){
    $data_string = json_encode($data);

    $url = "https://menurfx.herokuapp.com";

    $headr = array();
    $headr[] = 'Content-length: '.strlen( $data_string );
    $headr[] = 'Content-type: application/json';

    $ch = curl_init( $url );

    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headr );

    $result = curl_exec( $ch );
}

function pega_usuarios_painel($bloco){
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://automatips.com.br/api/Adm/getUsuarios?token=YBknWTkY6FUER0owiPffbMSucHbRvqFnSxgUR7TasBXEuW1YLqBda0wi2KgQO&tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'authority: automatips.com.br',
        'sec-ch-ua: "Google Chrome";v="95", "Chromium";v="95", ";Not A Brand";v="99"',
        'accept: application/json, text/javascript, */*; q=0.01',
        'content-type: application/json; charset=utf-8',
        'x-requested-with: XMLHttpRequest',
        'sec-ch-ua-mobile: ?0',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36',
        'sec-ch-ua-platform: "Windows"',
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'referer: https://automatips.com.br/v2/dashboardAdm.html',
        'accept-language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        'cookie: token="YBknWTkY6FUER0owiPffbMSucHbRvqFnSxgUR7TasBXEuW1YLqBda0wi2KgQO"; tokenAplicacao=JOS2F00AF043DBB75A3B12F28A5D4A1391A48EE9DD3DF424F840C63BCD3345CE02A; Servidor=http://automatips.com.br:7009; emailLogin=josealberto.gomes@hotmail.com; dtVen=2021-11-30T02:40:16Z'
      ),
    ));
    
    $response = json_decode(curl_exec($curl), TRUE)["Data"];
    
    
    curl_close($curl);
    
    $array_usuarios = array();
    
    date_default_timezone_set("America/Bahia");
    $hoje = strtotime(date("Y-m-d"));
    $i=0;
    foreach($response as $conta){
        $data_sync = strtotime(str_replace(["T", "Z"], " ", $conta['dataSync']));
        if($conta['statusPainel'] == 1 and $conta['tipsterFixo'] == $bloco and $data_sync > $hoje){
            $array_usuarios[$i]['numero'] = substr($conta['email'], strpos($conta['email'], '@gmail.com')-2, 2);
            $array_usuarios[$i]['email'] = $conta['email'];
            $array_usuarios[$i]['usuario'] = $conta['contaBet365'];
            $i++;
        }
    }
    
    
    array_multisort(array_map(function($element) {
        return $element['numero'];
    }, $array_usuarios), SORT_ASC, $array_usuarios);
    
    
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE contas";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    
    foreach($array_usuarios as $usuario){
        $numero = $usuario['numero'];
        $email = $usuario['email'];
        $conta = $usuario['usuario'];
        $adicionar_query = "INSERT INTO contas (numero, email, usuario) VALUES ('$numero', '$email', '$conta')";
        $adicionar_dados = pg_query($db_handle, $adicionar_query);
    }
    
    
    
    
    }

    function atualiza_contas(){
        $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
        $query = "SELECT * FROM contas";
        $rs = pg_query($db_handle, $query);
        $row = pg_fetch_all($rs);

        return $row;
    }

$APIurl = getenv('API_URL');
$token = getenv('TOKEN');

$requisicaocod = file_get_contents("php://input");
$requisicao = json_decode($requisicaocod, TRUE);
if(array_key_exists("messages", $requisicao)){
$texto = urlencode($requisicao["messages"][0]["body"]);

$minha = $requisicao["messages"][0]['fromMe'];



$db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
$conversa_query = "SELECT * FROM chat WHERE numero=1";
$seleciona_conversa = pg_query($db_handle, $conversa_query);
$array_conversa = pg_fetch_array($seleciona_conversa, 0);

if(!empty($texto) and empty($array_conversa['menu'])){
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Selecione a op????o desejada:*\n\n*1.* Reenviar apostas\n*2.* Religar todas as contas\n*3.* Verificar apostas\n*4.* ?????? Encerrar Aposta\n*5.* Atualizar contas\n*6.* Aviso de Reaberturas"));
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $menu = 1;
    $hora = time();
    $menu_query = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $seleciona_menu = pg_query($db_handle, $menu_query);
}else if($texto == "1" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*Digite o n??mero de alguma aposta para desligar as contas:*\n\n");
    $apostas = requisitar_apostas();
    
    foreach($apostas as $numero => $aposta){
        $mensagem = $mensagem.urlencode("*".$numero.".* ".$aposta['partida']." - ".$aposta['mercado']."\n");

    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    cadastra_apostas($apostas);
    $hora = time();
    $menu = 2;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if($texto == "4" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*?????? Digite o n??mero de alguma aposta para encerrar:*\n\n");
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
    $menu = 4;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if($texto == "6" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*Selecione a op????o desejada:*\n\n*1*. Adicionar partida\n*2*. Remover partida");
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $hora = time();
    $menu = 6;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if($texto == "1" and $array_conversa['menu'] == 6 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*Selecione a partida para avisar a reabertura:*\n\n");
    $partidas = seleciona_partidas_db("partidas");
    $col = array_column( $partidas, "numero" );
    array_multisort( $col, SORT_ASC, $partidas );
    foreach($partidas as $partida){
        $mensagem = $mensagem.urlencode("*".$partida['numero'].".* ".str_replace("**", "",$partida["partida"])."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $hora = time();
    $menu = 7;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if(is_numeric($texto) and $array_conversa['menu'] == 7 and ($array_conversa['hora'] + 1800) >= time()){
    $partidas = marca_partida_repassar("partidas", $texto, 1);
    $mensagem = urlencode("*Partida cadastrada com sucesso!*");
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $mensagem = urlencode("*Partidas cadastradas:*\n\n");
    $col = array_column( $partidas, "numero" );
    array_multisort( $col, SORT_ASC, $partidas );
    foreach($partidas as $partida){
        $mensagem = $mensagem.urlencode("*".$partida['numero'].".* ".str_replace("**", "",$partida["partida"])."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}else if($texto == "2" and $array_conversa['menu'] == 6 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*Selecione a partida para remover os avisos:*\n\n");
    $partidas = seleciona_partida_cadastrada("partidas");
    $col = array_column( $partidas, "numero" );
    array_multisort( $col, SORT_ASC, $partidas );
    foreach($partidas as $partida){
        $mensagem = $mensagem.urlencode("*".$partida['numero'].".* ".str_replace("**", "",$partida["partida"])."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $hora = time();
    $menu = 8;
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if(is_numeric($texto) and $array_conversa['menu'] == 8 and ($array_conversa['hora'] + 1800) >= time()){
    $partidas = marca_partida_repassar("partidas", $texto, 0);
    $mensagem = urlencode("*Partida removida com sucesso!*");
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $mensagem = urlencode("*Partidas cadastradas:*\n\n");
    $col = array_column( $partidas, "numero" );
    array_multisort( $col, SORT_ASC, $partidas );
    foreach($partidas as $partida){
        $mensagem = $mensagem.urlencode("*".$partida['numero'].".* ".str_replace("**", "",$partida["partida"])."\n");
    }
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}else if($texto == "3" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800) >= time()){
    $mensagem = urlencode("*?????? Iremos verificar as apostas do n??mero 1 at?? o n??mero que voc?? selecionar:*\n\n");
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
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Verificando apostas...*"));
    $mensagem = urlencode(verifica_apostas_concluidas(pega_partidas_db($texto)));
    if($mensagem != ""){
        file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".$mensagem);
    }else{
        file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("??? Todas as apostas foram enviadas com sucesso. Sem apostas duplicadas ou contas sem pegar!"));
    }
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}else if(is_numeric($texto) and $array_conversa['menu'] == 4 and ($array_conversa['hora'] + 1800) >= time()){
    $id = seleciona_id_aposta($texto);
    $partida = seleciona_partida_aposta($id);
    $menu = 5;
    $hora = time();
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*?????? Deseja realmente encerrar a seguinte aposta? ".$partida."*\n\n1. Sim\n2. N??o"));
    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $update_menu = "UPDATE chat SET hora='$hora', menu='$menu', numeropartida = '$texto' WHERE numero=1";
    $atualiza_menu = pg_query($db_handle, $update_menu);
}else if((strtolower($texto) == "sim" or $texto == "1") and $array_conversa['menu'] == 5 and ($array_conversa['hora'] + 1800) >= time()){
    $numeropartida = seleciona_numeropartida();
    $id2 = seleciona_id2($numeropartida);
    encerra_aposta($id2);
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*?????? Comando de encerrar enviado!*"));
    $usuarios_antigos = array();
    $id = seleciona_id_aposta($numeropartida);
    $partida = seleciona_partida_aposta($id);
    for($i=0;$i<5;$i++){
        sleep(5);
        $usuarios_antigos = verifica_usuario($id, $usuarios_antigos, $partida);
    }
    $data = array($id, $usuarios_antigos, $partida, 1);

    envia_dados($data);

    $db_handle = pg_connect("host=ec2-54-157-100-65.compute-1.amazonaws.com dbname=d6d3h3db6i6hh7 port=5432 user=imnnmotwerinrk password=8f266694114f8662be2ff79f02c184847aae067bdfda55dadeb077f49e2f60eb");
    $deletar_query = "TRUNCATE TABLE aposta";
    $deletar_dados = pg_query($db_handle, $deletar_query);
    $deletar2_query = "TRUNCATE TABLE chat";
    $deletar2_dados = pg_query($db_handle, $deletar2_query);
    $reiniciar =  "INSERT INTO chat (numero) VALUES (1)";
    $reiniciar_dados = pg_query($db_handle, $reiniciar);
}
else if(is_numeric($texto) and $array_conversa['menu'] == 2 and ($array_conversa['hora'] + 1800) >= time()){
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Desligando contas. Aguarde...*"));
    $id = seleciona_id_aposta($texto);
    $usuarios = verifica_usuarios($id);
    $contas_novas = atualiza_contas();
    $usuarios_menu = [];
    foreach($contas_novas as $conta){
        $usuarios_menu[$conta['usuario']][0] = $conta['numero'];
        $usuarios_menu[$conta['usuario']][1] = $conta['usuario'];
        $usuarios_menu[$conta['usuario']][2] = $conta['email'];
        $usuarios_menu[$conta['usuario']][3] = " ???";
    }
    $email_usuarios_pegaram = array();
    $mensagem = urlencode("*Status dos Usu??rios:*\n\n");
    foreach($usuarios as $usuario){
            $email_usuarios_pegaram[] = $usuarios_menu[$usuario][2];
            $array_usuarios = muda_usuario($usuarios_menu[$usuario][2], 0);
    }
    $status = verifica_status();

    foreach($status as $user){
        if(!empty($array_usuarios[$user['email']][0])){
        if($user['statusPainel'] == 0){
            $array_usuarios[$user['email']][2] = "???";
        }else{
            $array_usuarios[$user['email']][2] = "????";
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
    $mensagem = urlencode("*Usu??rios ligados:*\n\n");
    foreach($status as $user){
        if(!empty($array_usuarios[$user['email']][0])){
        if($user['statusPainel'] == 0){
            $array_usuarios[$user['email']][2] = "???";
        }else{
            $array_usuarios[$user['email']][2] = "????";
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
}else if($texto == "5" and $array_conversa['menu'] == 1 and ($array_conversa['hora'] + 1800)>= time()){
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Usu??rios sendo atualizados. Aguarde...*"));
    pega_usuarios_painel('60ad4808654e573f483cf80c');
    $contas = atualiza_contas();
    $mensagem = urlencode("*Usu??rios atualizados:*\n\n");
    foreach($contas as $usuario){
        $mensagem = $mensagem.urlencode($usuario['numero']." - ".$usuario['usuario']."  ????\n");
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
    file_get_contents($APIurl."sendMessage?token=".$token."&chatId=558399711150-1623374236@g.us&body=".urlencode("*Selecione a op????o desejada:*\n\n*1.* Reenviar apostas\n*2.* Religar todas as contas\n*3.* Verificar apostas\n*4.* ?????? Encerrar Aposta\n*5.* Atualizar contas\n*6.* Aviso de Reaberturas"));
    $menu = 1;
    $hora = time();
    $menu_query = "UPDATE chat SET hora='$hora', menu='$menu' WHERE numero=1";
    $seleciona_menu = pg_query($db_handle, $menu_query);
}
}else{
    $usuarios_antigos = $requisicao[1];
    $id = $requisicao[0];
    $partida = $requisicao[2];
    $j = $requisicao[3];
    for($i=0;$i<5;$i++){
        sleep(5);
        $usuarios_antigos = verifica_usuario($id, $usuarios_antigos, $partida);
    }
    $j++;
    if($j<=20){
        $data = array($id, $usuarios_antigos, $partida, $j);
        envia_dados($data);
    }
}
?>
