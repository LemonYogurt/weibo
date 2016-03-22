var redisClient = require('./config').redisClient;
var async = require('async');

/*
redis通用操作
 */

// keys通用命令字符
async.series({
    selectDB1: function (done) {
        redisClient.select(1, function (err) {
            done(err, 'success');
        });
    },
    selectDB2: function (done) {
        redisClient.select(0, function (err) {
            done(err, 'success');
        });
    },
    getKey: function (done) {
        redisClient.keys('*', function (err, results) {
            done(err, results);
        });
    },
    getKeySelect1: function (done) {
        redisClient.keys('na*', function (err, results) {
            done(err, results);
        });
    },
    getKeySelect2: function (done) {
        redisClient.keys('nam?e', function (err, results) {
            done(err, results);
        });
    },
    getKeySelect3: function (done) {
        redisClient.keys('nam[ea]', function (err, results) {
            done(err, results);
        });
    }
}, function (err, results) {
    console.log(results);
});
