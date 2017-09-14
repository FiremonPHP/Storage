# Storage
Este pacote visa oferecer um meio fácil para gerenciar arquivos no mongodb.

#### Requisitos

[FiremonPHP\Manager](https://github.com/FiremonPHP/Manager)

#### Connection
Uma vez configurada a conexão do manager, você poderá enviar, receber e deletar arquivos do mongo sem muito mistério.


#### Funcionalidades

``upload(array $files) : array``

Após enviar os arquivos, retornará um array ids dos objetos enviados.

**Nota mesmo inserindo os nomes dos arquivos, serão gerados hash aleatório para a indexação dos arquivos, portanto deve se guardar os ids recebidos!**

``download(array $filesId) : array``

Passando os ids guardardos/recebidos, você receberá os arquivos.

O retorno dessa função será semelhante a isto:

```
[ 0 => ['data' => resource, ['metadata' => []]]
```

``delete(array $filesId) : void``


```
<?php

require __DIR__.'/vendor/autoload.php';

FiremonPHP\Manager\Configuration::set('default',[
    'url' => 'mongodb://localhost:27017',
    'database' => 'testdb'
]);

$storage = new \FiremonPHP\Storage\Storage();

// Tanto com single files, como multiple funcionará!
$results = $storage->upload($_FILES['files']);

$results = $storage->upload([
   '9843213489124321' => [
       'data' => 'D*&GIUAW*D&77198dh1o210dj1',
       'metadata' => [
           'name' => 'marrocos',
           'public' => true
       ]
   ],
   '489d4qwqdq8d9' => [
       'data' => '78dhui192811hd1',
       'metadata' => [
           'name' => 'dubai',
           'public' => false,
           'owner_id' => '894d4q4989243'
       ]
   ] 
]);

/**
 * $results será um array com id de cada arquivo enviado
 */
print_r($result);

```
