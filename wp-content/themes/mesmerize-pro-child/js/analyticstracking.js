  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  var userId = user.id;
  console.log(userId)
  
  ga('create', 'UA-112683653-1', 'auto', {
      userId: userId
  });
  // alternativly:
//  ga('set', 'userId', user.id); // Legen Sie die User ID mithilfe des Parameters "user_id" des angemeldeten Nutzers fest.
  ga('send', 'pageview');