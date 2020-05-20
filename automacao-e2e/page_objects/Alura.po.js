const Helper = require("protractor-helper");

class Alura {
  constructor() {
    this.inputEmail = $("#login-email");
    this.inputPassword = $("#password");
    this.buttonEntrar = element(by.cssContainingText(".btn-login", "Entrar"));
    this.buttonGerenciarLicencas = $(".acquirementManagement__btn.acquirementManagement__btn--manage");
    this.gridAmbasNaoVisiveis = $(".acquirementManagement__form.acquirementManagement__form--hidden");
    this.semLicencaDisponivel = element(by.cssContainingText("[data-title='Licenças disponíveis']", "0"));
    this.todosOsMembrosAlocados = $$(".ms-elem-selection.ms-selected");
    this.todosOsMembrosNaoAlocados = $$("li[class='ms-elem-selectable']");
    this.buttonSalvarAlteracoes = $$("[value='Salvar alterações']").first();
    this.msgAlteracoesSalvasComSucesso = element(
      by.cssContainingText(".alert-message", "Alterações salvas com sucesso")
    );
  }

  acessar() {
    browser.get("");
  }

  logar(email, senha) {
    Helper.clearFieldAndFillItWithText(this.inputEmail, email);
    Helper.clearFieldAndFillItWithText(this.inputPassword, senha);
    Helper.click(this.buttonEntrar);
  }

  abrirGerenciadorDeLicenca() {
    Helper.click(this.buttonGerenciarLicencas);
    Helper.waitForElementNotToBePresent(this.gridAmbasNaoVisiveis);
  }

  tirarLicencaDeMembroAlocado(nome) {
    const dataName = `[data-name='${nome}']`;
    Helper.click($(`[class='ms-elem-selection ms-selected']${dataName}`));
    Helper.waitForElementVisibility($(`[class="ms-elem-selectable"]${dataName}`));
  }

  darLicencaAMembroNaoAlocado(nome) {
    const dataName = `[data-name='${nome}']`;
    Helper.click($(`[class='ms-elem-selectable']${dataName}`));
    Helper.waitForElementVisibility($(`[class="ms-elem-selection ms-selected"]${dataName}`));
  }

  tirarPrimeiroMembroDoArray(array) {
    let indice = array.indexOf(array[0]);
    return array.splice(indice, 1);
  }

  salvarAlteracoes() {
    Helper.click(this.buttonSalvarAlteracoes);
    Helper.waitForElementVisibility(this.msgAlteracoesSalvasComSucesso);
  }
}

module.exports = new Alura();
