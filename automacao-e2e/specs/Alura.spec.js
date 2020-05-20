const colors = require("colors");

const alura = require("../page_objects/Alura.po.js");
const conf = require("../../conf.js");
const manipulateJson = require("../src/manipulateJson.js");
const util = require("../src/util.js");

describe("Protractor", () => {
  beforeAll(() => browser.driver.manage().deleteAllCookies());

  it("Gerenciador de licenças do Alura", () => {
    let quantidadeUsuariosJson = manipulateJson.quantidadeUsuarios();
    if (quantidadeUsuariosJson < 1) util.erroPararExecucao("Não há usuários listados no JSON para receber licença");
    if (quantidadeUsuariosJson > conf.totalDeLicencas)
      util.erroPararExecucao(
        `Não há licenças suficientes. ${conf.totalDeLicencas} licença(s) e ${quantidadeUsuariosJson} usuário(s) no JSON`
      );

    alura.acessar();
    alura.logar(conf.acesso.email, conf.acesso.senha);
    alura.abrirGerenciadorDeLicenca();

    // Pegar quantidade de membros alocados
    alura.todosOsMembrosAlocados.getText().then(todosOsMembrosAlocados => {
      alura.todosOsMembrosNaoAlocados.getText().then(todosOsMembrosNaoAlocados => {
        const todosOsMembros = todosOsMembrosAlocados.concat(todosOsMembrosNaoAlocados)

        let usuariosDoJson = manipulateJson.pegarNomesDoJsonEmArray();
        console.log(`\nUsuários listados no json (${usuariosDoJson.length}):`);
        console.log(usuariosDoJson);

        console.log(`\nPessoas com licença no momento (${todosOsMembrosAlocados.length}):`);
        console.log(todosOsMembrosAlocados);

        let usuariosDoJsonValidos = [];
        todosOsMembros.filter(nome => {
          if (usuariosDoJson.indexOf(nome) !== -1){
            usuariosDoJsonValidos.push(nome)
          }
        })

        let membrosQueDevemReceberLicenca = usuariosDoJsonValidos.filter(obj => {
          return todosOsMembrosAlocados.indexOf(obj) == -1;
        });
        let membrosQuePodemPerderLicenca = todosOsMembrosAlocados.filter(obj => {
          return usuariosDoJsonValidos.indexOf(obj) == -1;
        });

        console.log(`\nPessoas que irão receber licença (${membrosQueDevemReceberLicenca.length}):`);
        console.log(membrosQueDevemReceberLicenca);

        console.log(
          `\nPessoas que estão disponíveis a terem a licença removida (${membrosQuePodemPerderLicenca.length}):`
        );
        console.log(membrosQuePodemPerderLicenca);

        // Executar enquanto existir gente para receber licença
        while (membrosQueDevemReceberLicenca.length > 0) {
          //executa se não tem licenca disponível e gente para receber licença
          alura.semLicencaDisponivel.isPresent().then(naoTemLicencaDisponivel => {
            if (naoTemLicencaDisponivel) {
              alura.tirarLicencaDeMembroAlocado(membrosQuePodemPerderLicenca[0]);
              let index = membrosQuePodemPerderLicenca.indexOf(membrosQuePodemPerderLicenca[0]);
              membrosQuePodemPerderLicenca.splice(index, 1);
            }
          });
          alura.darLicencaAMembroNaoAlocado(membrosQueDevemReceberLicenca[0]);
          let index = membrosQueDevemReceberLicenca.indexOf(membrosQueDevemReceberLicenca[0]);
          membrosQueDevemReceberLicenca.splice(index, 1);
        }
      });
    });

    if (conf.salvarAlteracao) {
      alura.salvarAlteracoes();
      alura.abrirGerenciadorDeLicenca();
    }

    alura.todosOsMembrosAlocados.getText().then(todosOsMembrosAlocados => {
      console.log(`\nPessoas com licença após as alterações (${todosOsMembrosAlocados.length}):`);
      console.log(todosOsMembrosAlocados);

      const todosOsMembrosDoJsonForamCadastrados = manipulateJson
        .pegarNomesDoJsonEmArray()
        .every(x => todosOsMembrosAlocados.includes(x));
      if (todosOsMembrosDoJsonForamCadastrados)
        console.log("\nSUCESSO!\nTodas as pessoas receberam a licença!".bold.green);
      else {
        const usuarioQueNaoRecebeuLicenca = manipulateJson
          .pegarNomesDoJsonEmArray()
          .filter(x => !todosOsMembrosAlocados.includes(x));

        util.erroPararExecucao(
          `NEM TODAS AS PESSOAS RECEBERAM A LICENÇA.\n\nUsuário(s) que deveria(m) ter licença:\n${usuarioQueNaoRecebeuLicenca}`
        );
      }
    });
  });
});
