var positions = [];
function getPositions() {
  // Register articles positions
  positions = [];
  jQuery('article').each(function() {
    var a = jQuery(this);
    positions.push([a, a.offset().top]);  
  }); 
}

jQuery(document).ready(function() {
  jQuery('#services div.fourcol:nth-child(3n)').addClass('last');
  jQuery('a[rel*=external]').each(function() {this.target = '_blank';});
  
  jQuery(window).resize(function() {
      jQuery('a.hide_more').click();
      jQuery('div.hide').readmore();
  });

  getPositions();
        
  // Register keypress events on the whole document
  jQuery(document).keydown(function(e) {
    var scrollTop = jQuery(document).scrollTop();
    switch(e.keyCode) { 
        // User pressed "up" arrow
        case 38:
          var prev = null;
          for(var i=0; i<positions.length; i++) {
            if (scrollTop - 10 > positions[i][1]) prev = positions[i][0];
            else break;
          }
          if (prev != null) {
            jQuery('html, body').animate({
              scrollTop: prev.offset().top
            }, 500);
          }
        break;
        // User pressed "down" arrow
        case 40:
          var next = null;
          for(var i=positions.length-1; i>=0; i--) {
            if (scrollTop + 10 < positions[i][1]) next = positions[i][0];
            else break;
          }
          if (next != null) {
            jQuery('html, body').animate({
              scrollTop: next.offset().top
            }, 500);
          }
        break;
    }
  });  
});

jQuery(window).load(function() {
  jQuery('div.hide').readmore();
  
});

jQuery('.wp-pagenavi a').live('click', function(){ //check when pagination link is clicked and stop its action.
  jQuery('.wp-pagenavi a').addClass('loading');
  var link = jQuery(this).attr('href'); //Get the href attribute
  jQuery('<span>').load(link + ' #content > div', function(response, status, xhr) {
    var res = jQuery('<span>').html(response.match(/<body[^>]*>([\s\S]*)<\/body>/mi)[1]).find('#content article');
    res.hide();
    
    jQuery('#work > div').before(res);
    res.fadeIn(function() {jQuery('.wp-pagenavi').remove(); res.find('.hide').readmore();});
  });
  return false;
});

jQuery.fn.readmore = function() {
    var numElements = this.length;
    var cont = 0;
  
    this.each(function() {
      cont++;
      if (numElements == cont) {
        getPositions();      
      }
      
      var $this = jQuery(this);
      var lH = parseInt($this.css('line-height').replace('px', ''));
      var parent = $this.parent();
      var parentPaddingBottom = parent.css('paddingBottom');
      parent.css({position: 'relative',paddingBottom: '0px'});
      var article = $this.parents('article:first');
      article.find('figure:eq(1)').parent().remove(); // Some empty figures remains when resizing
      var div = article.find('div:first');
      var title = parent.find('h2:first');
      var img = article.find('figure:first img');
      var hasVideo = false;
      if (img.length == 0) {
        img = article.find('.videowrap iframe:first');
        if (img.length == 1) {
          hasVideo = true;
        }
      }
      var figcaption = article.find('figure:first figcaption');
      article.height(img.height());
      var time = parent.find('time:first');
      var prevTop = $this.position().top;
      var imgWidth = img.width();
      var imgHeight = img.height();
      var maxHeight = img.height()-time.position().top-time.height()-30;
      if (title.position().left != 0) {
        var h1 = $this.height()+img.height()+time.position().top+time.height()+30;
        var h2 = 2*img.height();      
        article.height(h1<h2? h1:h2);
      }
      if ($this.height() < maxHeight) return;      
      maxHeight = lH*parseInt(maxHeight/lH);
      $this.css({overflow: 'hidden'}).height(maxHeight);
      article.find('.read_more, .hide_more').remove();
      var readmore = jQuery('<a href="#" title="'+home.read_more_title+'" class="read_more">'+home.read_more+'</a>')
        .click(function() {
          if (jQuery(this).hasClass('hide_more')) {
            if (hasVideo) article.find('div.videowrap').remove();
            var newsixcol = jQuery('<div class="sixcol '+(hasVideo?'videowrap':'')+'"></div>');
            if (!hasVideo) {
              newsixcol.append('<figure></figure>').find('figure').append(img.attr('style', '').height('auto')).append(figcaption);
            } else {
              img.height('auto');
              newsixcol.append(img.attr('style', '')).append(figcaption);
            }
            article.prepend(newsixcol);
            title.attr('style', '');
            parent.attr('style', '');
            time.attr('style', '');
            $this.attr('style', '');
            $this.readmore();
            getPositions();
            return false;
          } else {
            article.height('auto');
            parent.css({paddingBottom: parentPaddingBottom});
            $this.css({overflow: ''}).height('auto');
            jQuery(this)
              .toggleClass('read_more')
              .toggleClass('hide_more')
              .html(home.hide)
              .attr('title', jQuery(this).attr('title') == home.read_more_title? home.hide_title:home.read_more_title);
            if (title.position().left != 0) {
              article.css({overflow: ''});
              return false;
            }
            img.prependTo($this);
            figcaption.insertAfter(img);
            var mR = div.css('marginRight');
            parent.find('h2:first, time:first').css({marginLeft: (parseInt(imgWidth)+parseInt(mR))});
            $this.css({paddingLeft: mR});
            img.css({
                margin: '-'+(parseInt(prevTop)+parseInt(img.next().css('marginTop')))+'px '+mR+' '+(parseInt(mR)-parseInt(lH))+'px -'+mR, 
                padding: '0 0 0 -'+mR,
                'float': 'left',
                'width': imgWidth+'px'
              });
            if (hasVideo) img.css({'height': imgHeight+'px'});
            div.remove();
            getPositions();
            return false;
          }
        });
        
      article.append(readmore);
      if(numElements == 1) {
        jQuery('html, body').animate({
          scrollTop: article.offset().top
        }, 500);
      }
    });
  };


