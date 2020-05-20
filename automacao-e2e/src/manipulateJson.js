const fs = require("fs");
const path = require("path");

function lerJson() {
  return JSON.parse(fs.readFileSync(path.resolve(__dirname, "../../users.json"), { encoding: "utf8" }).toString());
}

function pegarNomesDoJsonEmArray() {
  let jsonToArray = [];
  for (let x = 0; x < quantidadeUsuarios(); x++) jsonToArray[x] = lerJson().users[x].nome;
  return jsonToArray;
}

const quantidadeUsuarios = () => {
  return lerJson().users.length;
};

module.exports = {
  pegarNomesDoJsonEmArray,
  quantidadeUsuarios
};
