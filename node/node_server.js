/*
  Requires dependencies installation
  See node_config.json for server settings (initially set up for localhost)
  Update _shows/copyright.js when server_url or private_key are changed
*/

var email = require('emailjs/email');
var http  = require('http');
var querystring = require('querystring');
var follow = require('follow');
var Recaptcha = require('recaptcha').Recaptcha;

var config = JSON.parse(require("fs").readFileSync("node_config.json", "UTF-8"));

var server = email.server.connect({
   user: config.email_user, 
   password: config.email_password, 
   host: config.email_host
});

function sendReport(doc) {
  var text = "Document : " + doc.link + "\n"
	+ "De : " + doc.email + "\n"
	+ (doc["wrong-ref"] === "true" ? "\nSignalé pour une référence erronée ou manquante (auteur, éditeur, licence...)\n" : "")
	+ (doc["is-protected"] === "true" ? "\nSignalé pour diffusion d'une œuvre protégée non-assimilable à une citation courte\n" : "")
	+ "\nDétails: " + doc.details;
  server.send({
	text: text, 
	from: config.email_sender, 
	to: config.email_receiver,
	subject: "Rapport de signalement TraduXio"
  });
  console.log("New copyright report: " + doc.link);
}

function processPost(request, response, callback) {
  if(request.method == 'POST') {
	var queryData = "";
    request.on('data', function(data) {
      queryData += data;
      if(queryData.length > 1e6) {
        queryData = "";
        response.writeHead(413, {'Content-Type': 'text/plain'}).end();
        request.connection.destroy();
      }
    }).on('end', function() {
      request.post = querystring.parse(queryData);
      callback(request, response);
    });
  } else {
      response.writeHead(405, {'Content-Type': 'text/plain'});
      response.end();
  }
}

function addDoc(doc) {
  delete doc.challenge;
  delete doc.captcha;
  var stringDoc = JSON.stringify(doc);
  var headers = {
	'Content-Type': 'application/json',
	'Content-Length': stringDoc.length
  };
  var options = {
	host: config.traduxio_host,
	port: config.traduxio_port,
	path: config.traduxio_database_path,
	method: 'POST',
	headers: headers
  };
  http.request(options).end(stringDoc);
}

function sendResponse(req, res) {
  var recaptcha = new Recaptcha(config.public_key, config.private_key, {
    remoteip:  req.connection.remoteAddress,
    challenge: req.post.challenge,
    response:  req.post.captcha
  });
  res.writeHead(200, {'Content-Type': 'text/plain', 'Access-Control-Allow-Origin': config.cors_access});
  recaptcha.verify(function(success, error_code) {
    if (success) {
	  addDoc(req.post);
	  res.end('valid');
    } else {
      res.end('Captcha invalide.');
    }
  });
}

function isReport(doc, req) {
  return doc.link !== undefined;
}

follow({
	db: config.database,
	since: "now",
	filter: isReport
  }, function(error, change) {
	if(!error) {
	  sendReport(change.doc);
	}
});

http.createServer(function (req, res) {
  processPost(req, res, sendResponse);
}).listen(config.port, config.server_url);

console.log("Started");