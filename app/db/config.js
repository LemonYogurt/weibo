var redis = require('redis');
var redisClient = redis.createClient(6379, '127.0.0.1');

redisClient.on('connect', function () {
    console.info('连接就绪');
});
redisClient.on('ready', function () {
    console.info('准备连接');
});
redisClient.on('reconnecting', function () {
    console.info('正在进行重连');
});
redisClient.on('error', function (err) {
    console.info('连接发生错误: ', err);
});
redisClient.on('end', function () {
    console.info('客户端关闭');
});

//setTimeout(function () {
//    redisClient.quit();
//}, 5000);

module.exports = {
    redisClient: redisClient
};