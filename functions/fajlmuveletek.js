// functions/fajlmuveletek.js
const fs = require("fs");
const path = require("path");

const usersFile = path.join(__dirname, "..", "json", "users.json");

function addUser({ fullName, username, password, email }) {
  try {
    // read existing file
    let data = fs.readFileSync(usersFile, "utf-8");
    let json = JSON.parse(data);

    // append new user
    json.users.push({
      fullName,
      username,
      password, // ⚠️ plain text, fine for hobby project
      email,
    });

    // write back to file
    fs.writeFileSync(usersFile, JSON.stringify(json, null, 4), "utf-8");

    console.log("✅ User added successfully!");
  } catch (err) {
    console.error("❌ Error while writing user:", err);
  }
}

module.exports = { addUser };
//$user = ["username" => "cimbi", "age" => 23, "password" => password_hash("123456", PASSWORD_DEFAULT)];

//save_users("../json/users.json", $user);