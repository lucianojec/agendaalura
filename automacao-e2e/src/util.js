const colors = require("colors");

const erroPararExecucao = mensagemDeErro => {
  console.log(`\nERRO:\n${mensagemDeErro}!\n`.bold.red);
  browser.driver.close().then(() => {
    process.exit(1);
  });
};

module.exports = { erroPararExecucao };
