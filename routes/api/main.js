var express = require('express');
var router = express.Router();

router.use(function(req, res, next) {
  console.log('Something is happening.');
  next();
});

router.get('/', function(req, res) {
  res.json({ message: 'hooray! welcome to our rest video api!' });  
});



router.route('/videos')

  .post(function(req, res) {

    var video = new Video();
    video.title = req.body.title;

    video.save(function(err) {
  if (err)
    res.send(err);

  res.json({ message: 'Video criado!' });
});


  })

  .get(function(req, res) {
    Video.find(function(err, videos) {
      if (err)
        res.send(err);

      res.json(videos);
    });
  });

module.exports.router = router;