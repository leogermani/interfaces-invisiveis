# interfaces-invisiveis

Códgo de exemplo usado na apresentação no WordCamp São Paulo 2020 sobre desenvolvimento de APIs com WordPress.

Neste repositório você encontra o código do plugin utilizado durante a apresentação, bem como a apresentação como PDF.

## Descrição dos arquivos

* `class-movies.php` - A primeira parte da apresentação: como registrar um post type e seus metadados de maneira que possam ser acessados e manipulados via API.
* `tests/test-api-movies.php` - A segunda parte da apresentação: como usar testes para interagir com a API e testar seu código.
* `class-filmes-endpoint.php` - A terceira parte da apresentação: Exemplo de como criar um endpoint personalizado para a API do WordPress
* `tests/test-api-filmes.php` - Bônus: Alguns exemplos de testes para esse endpoint personalizado.

Dentro de cada um desses arquivos o código está comentado para ajudar na sua compreensão.

## Instalação

Para rodar esse plugin basta baixá-lo na pasta de plugins e ativá-lo.

Uma vez ativo, você vai poder interagir com seu pela API.

## Ferramentas adicionais

* Postman -> Cliente de APIs para você poder fazer requisições de todos os tipos com facilidade. (postman.com)
* Basic Auth plugin -> Ative esse plugin para poder fazer requisições autenticadas para API. (https://github.com/WP-API/Basic-Auth)
* RestPlain -> Plugin que gera documentação da API automaticamente. (o repositório original está quebrado na última versão do WordPress, mas meu fork tá funcionando github.com/leogermani/Restsplain/)

## Fazendo requisições para a API

Existem várias maneiras de fazer requisições para a API. Você pode usar CURL na linha de comando, pode usar o Postman ou pode até usar o próprio navegador.

Por agora, vamos considerar que o Postman é o jeito mais fácil.

Faça requisições para:

* GET http://suesite/wp-json/v2/posts para ver os posts do seu site
* GET http://suesite/wp-json/v2/movie para ver os posts do tipo `movie` no seu site (post type criado por este plugin)

Para criar posts, mude o método para `POST` e informe os parâmetros obrigatórios para criar um post. Veja a documentação:

https://developer.wordpress.org/rest-api/reference/posts/

E o ótimo post do Felipe Elia: https://felipeelia.com.br/a-api-rest-do-wordpress/

## Rodando os testes

Para rodar os testes a gente vai usar o PHPUnit.

A montagem da estrutura de testes pra esse plugin foi feito usando o comando `wp scaffold plugin-tests`. Veja o tutorial aqui: https://make.wordpress.org/cli/handbook/misc/plugin-unit-tests/

Você vai precisar criar uma base de dados separada da sua instalação WordPress. Essa base de dados é zerada todas as vezes que você roda os testes!

Você também vai precisar ter instalado: 

* composer
* subversion (svn)

Pra começar, vamos instalar o phpunit pelo composer. (Se você já tem o phpunit instalado, não precisa disso)

`composer install`

Já criou a base de dados pra teste? Se não, cria agora.

Agora vamos preparar a instalação de testes do WordPress. Pra isso, você vai rodar o seguinte comando:

```
./bin/install-wp-tests.sh wordpress_test root root localhost latest
```

Nesse comando você vai precisar substituir os argumentos pelo do seu ambiente

* `wordpress_test` é o nome da base de dados de teste
* `root` é o nome de usuáraio do MySQL
* `root` é a senha do usuário do MySQL
* `localhost` é o host do MySQL
* `latest` é a versão do WordPress (se você quiser testar o plugin com alguma outra versão do WP, mude esse parâmetro)

Esse comando vai baixar o WordPress para uma pasta temporária no sem computador. Depois de reiniciar, possivelmente você vai precisar rodar isso de novo pra rodar os testes de novo.

Agora, pra rodar os testes, é só rodar:

`composer run phpunit`

Se você já tinha o phpunit instalado globalmente no seu ambiente, basta rodar `phpunit`.
