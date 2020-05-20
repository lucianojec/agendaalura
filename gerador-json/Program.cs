using MySql.Data.MySqlClient;
using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Data;
using System.IO;
using System.Web.Mvc;

namespace AgendaAluraJson
{
    class Program
    {
        static void Main(string[] args)
        {
            string connString = " Persist Security Info= False; server=172.21.10.7;database=celke;uid=root;pwd=softplan2019";

            using (MySqlConnection connection = new MySqlConnection(connString))
            {
                Console.WriteLine("Conectado na Base!");
                
                string customerCommandText = "select distinct(title) as nome from events where end > date_add(now(), interval 30 MINUTE) and (start <= now() or start <= date_add(now(), interval 30 MINUTE))";
                MySqlDataAdapter customerAdapter = new MySqlDataAdapter(customerCommandText, connection);                
                Console.WriteLine("Realizado o Select!");

                DataSet customerOrders = new DataSet();
                customerAdapter.Fill(customerOrders, "users");
                Console.WriteLine("Preparado Json!");

                string json = JsonConvert.SerializeObject(customerOrders, Formatting.Indented);

                string dir = Directory.GetCurrentDirectory() + "/../../../../";
                if (!Directory.Exists(dir))
                    Directory.CreateDirectory(dir);                
                string path = dir + "/users.json";

                StreamWriter jjson = new StreamWriter(path);
                
                jjson.Write(json);
                jjson.Close();
                Console.WriteLine("Gerado arquivo Json!");

                Console.WriteLine(File.Exists(path));

            }
        }
    }
}
