var redisClient = require('./config').redisClient;
var async = require('async');

/*
redis通用操作
 */

// keys通用命令字符

// ①：根据匹配模式获取key
async.series({
    // 切换数据库
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
    },
    getKeySelect4: function (done) {
        redisClient.keys('name', function (err, results) {
            done(err, results);
        });
    },
    // ②：randomkey随机返回key
    getRandomKey: function (done) {
        redisClient.randomkey(function (err, result) {
            console.log(err);
            console.log(result);
            done(err, result);
        });
    },
    // ③：返回key的type类型
    getKeyType: function (done) {
        redisClient.type('name', function (err, result) {
            done(err, result);
        });
    },
    // ④：查看某个键是否存在
    isExists: function (done) {
        redisClient.exists('ui', function (err, result) {
            // 1表示存在
            // 0表示不存在
            done(err, result);
        });
    },
    // ⑤：删除key
    delKey: function (done) {
        // 通过传入一个数组可以删除多个key
        redisClient.del(['a', 'b', 'c'], function (err, results) {
            done(err, results);
        });
    }
}, function (err, results) {
    console.log(results);
});



