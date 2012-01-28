/*
http.createServer(function(req, res) {
	res.writeHead(200, {'Content-Type' : 'text/html'});
	res.end('<h2>Test</h2>');
}).listen(1337, "127.0.0.1");

*/
var mongoskin = require('mongoskin');
var express = require('express');
var less = require('less')
var parser = new(less.Parser);
var app = express.createServer();
//var mongourl = "perchouli:Forelord12@kanoya-perchouli-data-0.dotcloud.com:20661/kanoya?auto_reconnect";
var mongourl = "localhost:27017/olgg";
var db = mongoskin.db(mongourl);
//db.bind('items');

app.configure(function(){
    //Static
    app.use(express.compiler({ src: __dirname + '/public/css', enable: ['less']}));
    app.use(express.static(__dirname + '/public'));
    
    //Middleware
    app.use(express.bodyParser());
    app.use(express.methodOverride());
    app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
    
    //Template
    app.set('views', __dirname + '/views');
    app.set('view engine', 'html');
    app.register(".html", require("jqtpl").express);

});
app.configure('production', function(){
    var oneYear = 31557600000;
    app.use(express.static(__dirname + '/public', { maxAge: oneYear }));
    app.use(express.errorHandler());
});

//Routing
app.get('/', function(req, res){
    db.collection('items').find().toArray(function(err, items){
        res.render('item', { pageTitle: 'Items', 'shopItems' :JSON.stringify(items) });
    })
});

app.get('/roles/create', function(req, res){
    res.render('roles/create', { pageTitle: '创建角色' });
});

app.get('/roles/create/default', function(req, res){
    rolePos = ['正面1','正面2','正面3','侧面1', '侧面2', '侧面3'];
    clothType = {
      'unusual' : [{4: '中式旗袍'}, {2: '修女'}],
      'schooluniform' : [{8 :'校服1'}]
    };
    res.render('roles/default', { 'rolePos': rolePos, 'clothType': JSON.stringify(clothType) });
});

app.post('/', function(req, res){
    console.log(req);
});
app.listen(3000)

