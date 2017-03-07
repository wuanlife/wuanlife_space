;(function(window) {

  var svgSprite = '<svg>' +
    '' +
    '<symbol id="icon-fanhui" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M243.2 448L601.6 89.6 512 0 0 512l512 512 89.6-89.6L243.2 576H1024v-128z" fill="#5677FC" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-chuangjianxingqiu" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-fanhui1" viewBox="0 0 1280 1024">' +
    '' +
    '<path d="M399.36 563.2l286.72-286.72L614.4 204.8 204.8 614.4l409.6 409.6 71.68-71.68L399.36 665.6H1024v-102.4z" fill="#FFFFFF" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-hongdian" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M512 512m-512 0a512 512 0 1 0 1024 0 512 512 0 1 0-1024 0Z" fill="#FF0000" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-fasong" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-pinglun" viewBox="0 0 1117 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-qinglvxingqiu" viewBox="0 0 1385 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-pinglun1" viewBox="0 0 1117 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-qinglvxingqiu1" viewBox="0 0 1385 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-shoucang" viewBox="0 0 1068 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-quanbuxingqiu" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-shoucang1" viewBox="0 0 1068 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-simixingqiushenqing" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-sousuo" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M731.725229 643.835138H685.279484l-16.144869-16.057125a378.643969 378.643969 0 0 0 91.780069-247.349919C760.885436 170.310817 590.574619 0 380.428094 0 170.310817 0 0 170.310817 0 380.428094c0 210.146525 170.310817 380.457342 380.428094 380.457342 94.529376 0 180.752335-34.658822 247.291423-91.663077l16.174116 16.027877v46.358001l292.508755 292.128531L1023.678273 936.460884l-291.953044-292.625746z m-351.267887 0a263.3193 263.3193 0 0 1-263.407044-263.407044 263.407044 263.407044 0 1 1 263.377796 263.377796z" fill="#5366D3" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-sousuo1" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M731.725229 643.835138H685.279484l-16.144869-16.057125a378.643969 378.643969 0 0 0 91.780069-247.349919C760.885436 170.310817 590.574619 0 380.428094 0 170.310817 0 0 170.310817 0 380.428094c0 210.146525 170.310817 380.457342 380.428094 380.457342 94.529376 0 180.752335-34.658822 247.291423-91.663077l16.174116 16.027877v46.358001l292.508755 292.128531L1023.678273 936.460884l-291.953044-292.625746z m-351.267887 0a263.3193 263.3193 0 0 1-263.407044-263.407044 263.407044 263.407044 0 1 1 263.377796 263.377796z" fill="#FFFFFF" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-tiezitongzhi" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-tianjia" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-tupian" viewBox="0 0 1234 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xia" viewBox="0 0 1331 1024">' +
    '' +
    '<path d="M42.0352 211.8656C-37.7344 94.8736 11.8272 0 153.8048 0h1023.5904c141.4656 0 191.3856 95.0784 111.7696 211.8656L809.984 914.5344c-79.7696 116.992-209.2544 116.7872-288.8704 0L41.984 211.8656z" fill="#000000" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-wode" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-wode1" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xiaoxi" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xiaoxi1" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xingqiutongzhi" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-you" viewBox="0 0 1024 1024">' +
    '' +
    '<path d="M281.127385 991.665231C191.133538 1053.026462 118.153846 1014.902154 118.153846 905.688615V118.311385C118.153846 9.491692 191.291077-28.908308 281.127385 32.334769l540.514461 368.561231c89.993846 61.361231 89.836308 160.964923 0 222.208L281.127385 991.704615z" fill="#000000" ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xingbie" viewBox="0 0 50176 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-zan" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-zan1" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-zhuye" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-zhuye1" viewBox="0 0 1024 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '<symbol id="icon-xingbie1" viewBox="0 0 50176 1024">' +
    '' +
    '<path d=""  ></path>' +
    '' +
    '</symbol>' +
    '' +
    '</svg>'
  var script = function() {
    var scripts = document.getElementsByTagName('script')
    return scripts[scripts.length - 1]
  }()
  var shouldInjectCss = script.getAttribute("data-injectcss")

  /**
   * document ready
   */
  var ready = function(fn) {
    if (document.addEventListener) {
      if (~["complete", "loaded", "interactive"].indexOf(document.readyState)) {
        setTimeout(fn, 0)
      } else {
        var loadFn = function() {
          document.removeEventListener("DOMContentLoaded", loadFn, false)
          fn()
        }
        document.addEventListener("DOMContentLoaded", loadFn, false)
      }
    } else if (document.attachEvent) {
      IEContentLoaded(window, fn)
    }

    function IEContentLoaded(w, fn) {
      var d = w.document,
        done = false,
        // only fire once
        init = function() {
          if (!done) {
            done = true
            fn()
          }
        }
        // polling for no errors
      var polling = function() {
        try {
          // throws errors until after ondocumentready
          d.documentElement.doScroll('left')
        } catch (e) {
          setTimeout(polling, 50)
          return
        }
        // no errors, fire

        init()
      };

      polling()
        // trying to always fire before onload
      d.onreadystatechange = function() {
        if (d.readyState == 'complete') {
          d.onreadystatechange = null
          init()
        }
      }
    }
  }

  /**
   * Insert el before target
   *
   * @param {Element} el
   * @param {Element} target
   */

  var before = function(el, target) {
    target.parentNode.insertBefore(el, target)
  }

  /**
   * Prepend el to target
   *
   * @param {Element} el
   * @param {Element} target
   */

  var prepend = function(el, target) {
    if (target.firstChild) {
      before(el, target.firstChild)
    } else {
      target.appendChild(el)
    }
  }

  function appendSvg() {
    var div, svg

    div = document.createElement('div')
    div.innerHTML = svgSprite
    svgSprite = null
    svg = div.getElementsByTagName('svg')[0]
    if (svg) {
      svg.setAttribute('aria-hidden', 'true')
      svg.style.position = 'absolute'
      svg.style.width = 0
      svg.style.height = 0
      svg.style.overflow = 'hidden'
      prepend(svg, document.body)
    }
  }

  if (shouldInjectCss && !window.__iconfont__svg__cssinject__) {
    window.__iconfont__svg__cssinject__ = true
    try {
      document.write("<style>.svgfont {display: inline-block;width: 1em;height: 1em;fill: currentColor;vertical-align: -0.1em;font-size:16px;}</style>");
    } catch (e) {
      console && console.log(e)
    }
  }

  ready(appendSvg)


})(window)