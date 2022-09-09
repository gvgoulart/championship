<h1>Rodar o projeto:</h1>

```
docker build . && ./vendor/bin/sail up
```


No shell do docker, será necessário rodar as migrations e as seeds com o comando:

```
php artisan migrate && php artisan db:seed
```

O insomnia do projeto esta na raiz.

Criterios de Aceite:
- [x] Deve ser possível inserir os oito times participantes do campeonato.
- [x] Não deve ser possível simular um campeonato com mais ou menos de oito times.
- [x] A aplicação deve fazer o chaveamento do campeonato (É feito um embaralhamento antes.).
- [x] A aplicação deve simular o resultado de cada partida (O resultado é simulado com o script fornecido).
- [x] A aplicação deve calcular a pontuação de cada time.
- [x] A aplicação deve simular o time vencedor do campeonato.
- [x] Deve ser possível recuperar as informações de campeonatos anteriores.

Diferenciais:
- [x] Implementação de chamada do script Python (Instalei o python via docker e utilizei o mesmo com a função shell_exec, caso de algum erro, gerei o placar com função php):
- [ ] Implementação de critério de desempate.
- [ ] Testes de integração (Realizei alguns testes unitários, porém, por falta de tempo faltaram alguns.)
- [x] Containerização da aplicação (Utilizei o Sail, mas também adicionei o adminer e o phpadmin por preferencia.).
- [x] Envio de documentação.

Neste projeto, é possível realizar um CRUD de times e de campeonatos, os nomes dos dois são únicos.

Para simularmos o campeonato, utilizamos em base 3 rotas, são elas:

```
insertTeamsOnAChampionship       api/championship/{championship_id}/insertTeams
```

que alimenta o campeonato com times de sua preferencia, máx 8.


```
sortAndCreateGames              api/championship/{championship_id}/sort
```

que irá sortear as quartas de finais caso os 8 times ainda estejam disponíveis, caso existam 4 times eliminados ela sorteia as semis e caso tenha sobrado apenas 2 times, ela simula o terceiro lugar e gera o jogo da final.


```
runGames                       api/games/{championship_id}/run
```

Essa rota gera os resultados dos jogos que não foram iniciados no campeonato.
