const conf = require("../conf.js");

module.exports.config = {
  directConnect: true,
  baseUrl: `https://cursos.alura.com.br/company/594/management?teamId=${conf.teamId}`,
  specs: ["specs/*.spec.js"],
  capabilities: {
    browserName: "chrome",
    chromeOptions: {
      args: ["--headless", "--disable-extensions", "--disable-plugins"]
    }
  },
  onPrepare: () => {
    browser.waitForAngularEnabled(false);
    browser.driver
      .manage()
      .window()
      .maximize();
  }
};
