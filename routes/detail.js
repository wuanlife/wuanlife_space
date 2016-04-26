var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');

/* GET users listing. */
router.get('/:id', function(req, res, next) {
  res.send('respond with a resource');
});

module.exports = router;
