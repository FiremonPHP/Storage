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
