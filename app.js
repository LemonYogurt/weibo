var express = require('express');
var path = require('path');

var app = express();
var PORT = process.env.PORT || 3000;

app.use(express.static(path.join(__dirname, './public')));
app.listen(PORT, function () {
    console.log('listening port ', PORT, ' success');
});