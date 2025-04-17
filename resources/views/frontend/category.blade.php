@extends('frontend.layouts.main')

@section('main-section')
<!DOCTYPE html>
<html lang="en-US" class="no-js">
  <head>
    <meta charset="UTF-8" />
    <script>
      if (
        navigator.userAgent.match(/MSIE|Internet Explorer/i) ||
        navigator.userAgent.match(/Trident\/7\..*?rv:11/i)
      ) {
        var href = document.location.href;
        if (!href.match(/[?&]nowprocket/)) {
          if (href.indexOf("?") == -1) {
            if (href.indexOf("#") == -1) {
              document.location.href = href + "?nowprocket=1";
            } else {
              document.location.href = href.replace("#", "?nowprocket=1#");
            }
          } else {
            if (href.indexOf("#") == -1) {
              document.location.href = href + "&nowprocket=1";
            } else {
              document.location.href = href.replace("#", "&nowprocket=1#");
            }
          }
        }
      }
    </script>
    <script>
      class RocketLazyLoadScripts {
        constructor() {
          (this.v = "1.2.3"),
            (this.triggerEvents = [
              "keydown",
              "mousedown",
              "mousemove",
              "touchmove",
              "touchstart",
              "touchend",
              "wheel",
            ]),
            (this.userEventHandler = this._triggerListener.bind(this)),
            (this.touchStartHandler = this._onTouchStart.bind(this)),
            (this.touchMoveHandler = this._onTouchMove.bind(this)),
            (this.touchEndHandler = this._onTouchEnd.bind(this)),
            (this.clickHandler = this._onClick.bind(this)),
            (this.interceptedClicks = []),
            window.addEventListener("pageshow", (t) => {
              this.persisted = t.persisted;
            }),
            window.addEventListener("DOMContentLoaded", () => {
              this._preconnect3rdParties();
            }),
            (this.delayedScripts = { normal: [], async: [], defer: [] }),
            (this.trash = []),
            (this.allJQueries = []);
        }
        _addUserInteractionListener(t) {
          if (document.hidden) {
            t._triggerListener();
            return;
          }
          this.triggerEvents.forEach((e) =>
            window.addEventListener(e, t.userEventHandler, { passive: !0 })
          ),
            window.addEventListener("touchstart", t.touchStartHandler, {
              passive: !0,
            }),
            window.addEventListener("mousedown", t.touchStartHandler),
            document.addEventListener("visibilitychange", t.userEventHandler);
        }
        _removeUserInteractionListener() {
          this.triggerEvents.forEach((t) =>
            window.removeEventListener(t, this.userEventHandler, {
              passive: !0,
            })
          ),
            document.removeEventListener(
              "visibilitychange",
              this.userEventHandler
            );
        }
        _onTouchStart(t) {
          "HTML" !== t.target.tagName &&
            (window.addEventListener("touchend", this.touchEndHandler),
            window.addEventListener("mouseup", this.touchEndHandler),
            window.addEventListener("touchmove", this.touchMoveHandler, {
              passive: !0,
            }),
            window.addEventListener("mousemove", this.touchMoveHandler),
            t.target.addEventListener("click", this.clickHandler),
            this._renameDOMAttribute(t.target, "onclick", "rocket-onclick"),
            this._pendingClickStarted());
        }
        _onTouchMove(t) {
          window.removeEventListener("touchend", this.touchEndHandler),
            window.removeEventListener("mouseup", this.touchEndHandler),
            window.removeEventListener("touchmove", this.touchMoveHandler, {
              passive: !0,
            }),
            window.removeEventListener("mousemove", this.touchMoveHandler),
            t.target.removeEventListener("click", this.clickHandler),
            this._renameDOMAttribute(t.target, "rocket-onclick", "onclick"),
            this._pendingClickFinished();
        }
        _onTouchEnd(t) {
          window.removeEventListener("touchend", this.touchEndHandler),
            window.removeEventListener("mouseup", this.touchEndHandler),
            window.removeEventListener("touchmove", this.touchMoveHandler, {
              passive: !0,
            }),
            window.removeEventListener("mousemove", this.touchMoveHandler);
        }
        _onClick(t) {
          t.target.removeEventListener("click", this.clickHandler),
            this._renameDOMAttribute(t.target, "rocket-onclick", "onclick"),
            this.interceptedClicks.push(t),
            t.preventDefault(),
            t.stopPropagation(),
            t.stopImmediatePropagation(),
            this._pendingClickFinished();
        }
        _replayClicks() {
          window.removeEventListener("touchstart", this.touchStartHandler, {
            passive: !0,
          }),
            window.removeEventListener("mousedown", this.touchStartHandler),
            this.interceptedClicks.forEach((t) => {
              t.target.dispatchEvent(
                new MouseEvent("click", {
                  view: t.view,
                  bubbles: !0,
                  cancelable: !0,
                })
              );
            });
        }
        _waitForPendingClicks() {
          return new Promise((t) => {
            this._isClickPending ? (this._pendingClickFinished = t) : t();
          });
        }
        _pendingClickStarted() {
          this._isClickPending = !0;
        }
        _pendingClickFinished() {
          this._isClickPending = !1;
        }
        _renameDOMAttribute(t, e, r) {
          t.hasAttribute &&
            t.hasAttribute(e) &&
            (event.target.setAttribute(r, event.target.getAttribute(e)),
            event.target.removeAttribute(e));
        }
        _triggerListener() {
          this._removeUserInteractionListener(this),
            "loading" === document.readyState
              ? document.addEventListener(
                  "DOMContentLoaded",
                  this._loadEverythingNow.bind(this)
                )
              : this._loadEverythingNow();
        }
        _preconnect3rdParties() {
          let t = [];
          document
            .querySelectorAll("script[type=rocketlazyloadscript]")
            .forEach((e) => {
              if (e.hasAttribute("src")) {
                let r = new URL(e.src).origin;
                r !== location.origin &&
                  t.push({
                    src: r,
                    crossOrigin:
                      e.crossOrigin ||
                      "module" === e.getAttribute("data-rocket-type"),
                  });
              }
            }),
            (t = [...new Map(t.map((t) => [JSON.stringify(t), t])).values()]),
            this._batchInjectResourceHints(t, "preconnect");
        }
        async _loadEverythingNow() {
          (this.lastBreath = Date.now()),
            this._delayEventListeners(this),
            this._delayJQueryReady(this),
            this._handleDocumentWrite(),
            this._registerAllDelayedScripts(),
            this._preloadAllScripts(),
            await this._loadScriptsFromList(this.delayedScripts.normal),
            await this._loadScriptsFromList(this.delayedScripts.defer),
            await this._loadScriptsFromList(this.delayedScripts.async);
          try {
            await this._triggerDOMContentLoaded(),
              await this._triggerWindowLoad();
          } catch (t) {
            console.error(t);
          }
          window.dispatchEvent(new Event("rocket-allScriptsLoaded")),
            this._waitForPendingClicks().then(() => {
              this._replayClicks();
            }),
            this._emptyTrash();
        }
        _registerAllDelayedScripts() {
          document
            .querySelectorAll("script[type=rocketlazyloadscript]")
            .forEach((t) => {
              t.hasAttribute("data-rocket-src")
                ? t.hasAttribute("async") && !1 !== t.async
                  ? this.delayedScripts.async.push(t)
                  : (t.hasAttribute("defer") && !1 !== t.defer) ||
                    "module" === t.getAttribute("data-rocket-type")
                  ? this.delayedScripts.defer.push(t)
                  : this.delayedScripts.normal.push(t)
                : this.delayedScripts.normal.push(t);
            });
        }
        async _transformScript(t) {
          return new Promise(
            (await this._littleBreath(),
            navigator.userAgent.indexOf("Firefox/") > 0 ||
              "" === navigator.vendor)
              ? (e) => {
                  let r = document.createElement("script");
                  [...t.attributes].forEach((t) => {
                    let e = t.nodeName;
                    "type" !== e &&
                      ("data-rocket-type" === e && (e = "type"),
                      "data-rocket-src" === e && (e = "src"),
                      r.setAttribute(e, t.nodeValue));
                  }),
                    t.text && (r.text = t.text),
                    r.hasAttribute("src")
                      ? (r.addEventListener("load", e),
                        r.addEventListener("error", e))
                      : ((r.text = t.text), e());
                  try {
                    t.parentNode.replaceChild(r, t);
                  } catch (i) {
                    e();
                  }
                }
              : async (e) => {
                  function r() {
                    t.setAttribute("data-rocket-status", "failed"), e();
                  }
                  try {
                    let i = t.getAttribute("data-rocket-type"),
                      n = t.getAttribute("data-rocket-src");
                    t.text,
                      i
                        ? ((t.type = i), t.removeAttribute("data-rocket-type"))
                        : t.removeAttribute("type"),
                      t.addEventListener("load", function r() {
                        t.setAttribute("data-rocket-status", "executed"), e();
                      }),
                      t.addEventListener("error", r),
                      n
                        ? (t.removeAttribute("data-rocket-src"), (t.src = n))
                        : (t.src =
                            "data:text/javascript;base64," +
                            window.btoa(unescape(encodeURIComponent(t.text))));
                  } catch (s) {
                    r();
                  }
                }
          );
        }
        async _loadScriptsFromList(t) {
          let e = t.shift();
          return e && e.isConnected
            ? (await this._transformScript(e), this._loadScriptsFromList(t))
            : Promise.resolve();
        }
        _preloadAllScripts() {
          this._batchInjectResourceHints(
            [
              ...this.delayedScripts.normal,
              ...this.delayedScripts.defer,
              ...this.delayedScripts.async,
            ],
            "preload"
          );
        }
        _batchInjectResourceHints(t, e) {
          var r = document.createDocumentFragment();
          t.forEach((t) => {
            let i =
              (t.getAttribute && t.getAttribute("data-rocket-src")) || t.src;
            if (i) {
              let n = document.createElement("link");
              (n.href = i),
                (n.rel = e),
                "preconnect" !== e && (n.as = "script"),
                t.getAttribute &&
                  "module" === t.getAttribute("data-rocket-type") &&
                  (n.crossOrigin = !0),
                t.crossOrigin && (n.crossOrigin = t.crossOrigin),
                t.integrity && (n.integrity = t.integrity),
                r.appendChild(n),
                this.trash.push(n);
            }
          }),
            document.head.appendChild(r);
        }
        _delayEventListeners(t) {
          let e = {};
          function r(t, r) {
            !(function t(r) {
              !e[r] &&
                ((e[r] = {
                  originalFunctions: {
                    add: r.addEventListener,
                    remove: r.removeEventListener,
                  },
                  eventsToRewrite: [],
                }),
                (r.addEventListener = function () {
                  (arguments[0] = i(arguments[0])),
                    e[r].originalFunctions.add.apply(r, arguments);
                }),
                (r.removeEventListener = function () {
                  (arguments[0] = i(arguments[0])),
                    e[r].originalFunctions.remove.apply(r, arguments);
                }));
              function i(t) {
                return e[r].eventsToRewrite.indexOf(t) >= 0 ? "rocket-" + t : t;
              }
            })(t),
              e[t].eventsToRewrite.push(r);
          }
          function i(t, e) {
            let r = t[e];
            Object.defineProperty(t, e, {
              get: () => r || function () {},
              set(i) {
                t["rocket" + e] = r = i;
              },
            });
          }
          r(document, "DOMContentLoaded"),
            r(window, "DOMContentLoaded"),
            r(window, "load"),
            r(window, "pageshow"),
            r(document, "readystatechange"),
            i(document, "onreadystatechange"),
            i(window, "onload"),
            i(window, "onpageshow");
        }
        _delayJQueryReady(t) {
          let e;
          function r(r) {
            if (r && r.fn && !t.allJQueries.includes(r)) {
              r.fn.ready = r.fn.init.prototype.ready = function (e) {
                return (
                  t.domReadyFired
                    ? e.bind(document)(r)
                    : document.addEventListener("rocket-DOMContentLoaded", () =>
                        e.bind(document)(r)
                      ),
                  r([])
                );
              };
              let i = r.fn.on;
              (r.fn.on = r.fn.init.prototype.on =
                function () {
                  if (this[0] === window) {
                    function t(t) {
                      return t
                        .split(" ")
                        .map((t) =>
                          "load" === t || 0 === t.indexOf("load.")
                            ? "rocket-jquery-load"
                            : t
                        )
                        .join(" ");
                    }
                    "string" == typeof arguments[0] ||
                    arguments[0] instanceof String
                      ? (arguments[0] = t(arguments[0]))
                      : "object" == typeof arguments[0] &&
                        Object.keys(arguments[0]).forEach((e) => {
                          let r = arguments[0][e];
                          delete arguments[0][e], (arguments[0][t(e)] = r);
                        });
                  }
                  return i.apply(this, arguments), this;
                }),
                t.allJQueries.push(r);
            }
            e = r;
          }
          r(window.jQuery),
            Object.defineProperty(window, "jQuery", {
              get: () => e,
              set(t) {
                r(t);
              },
            });
        }
        async _triggerDOMContentLoaded() {
          (this.domReadyFired = !0),
            await this._littleBreath(),
            document.dispatchEvent(new Event("rocket-DOMContentLoaded")),
            await this._littleBreath(),
            window.dispatchEvent(new Event("rocket-DOMContentLoaded")),
            await this._littleBreath(),
            document.dispatchEvent(new Event("rocket-readystatechange")),
            await this._littleBreath(),
            document.rocketonreadystatechange &&
              document.rocketonreadystatechange();
        }
        async _triggerWindowLoad() {
          await this._littleBreath(),
            window.dispatchEvent(new Event("rocket-load")),
            await this._littleBreath(),
            window.rocketonload && window.rocketonload(),
            await this._littleBreath(),
            this.allJQueries.forEach((t) =>
              t(window).trigger("rocket-jquery-load")
            ),
            await this._littleBreath();
          let t = new Event("rocket-pageshow");
          (t.persisted = this.persisted),
            window.dispatchEvent(t),
            await this._littleBreath(),
            window.rocketonpageshow &&
              window.rocketonpageshow({ persisted: this.persisted });
        }
        _handleDocumentWrite() {
          let t = new Map();
          document.write = document.writeln = function (e) {
            let r = document.currentScript;
            r || console.error("WPRocket unable to document.write this: " + e);
            let i = document.createRange(),
              n = r.parentElement,
              s = t.get(r);
            void 0 === s && ((s = r.nextSibling), t.set(r, s));
            let a = document.createDocumentFragment();
            i.setStart(a, 0),
              a.appendChild(i.createContextualFragment(e)),
              n.insertBefore(a, s);
          };
        }
        async _littleBreath() {
          Date.now() - this.lastBreath > 45 &&
            (await this._requestAnimFrame(), (this.lastBreath = Date.now()));
        }
        async _requestAnimFrame() {
          return document.hidden
            ? new Promise((t) => setTimeout(t))
            : new Promise((t) => requestAnimationFrame(t));
        }
        _emptyTrash() {
          this.trash.forEach((t) => t.remove());
        }
        static run() {
          let t = new RocketLazyLoadScripts();
          t._addUserInteractionListener(t);
        }
      }
      RocketLazyLoadScripts.run();
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="profile" href="//gmpg.org/xfn/11" />
    <script type="rocketlazyloadscript">
      document.documentElement.className = document.documentElement.className + ' yes-js js_active js'
    </script>
    <title>Sponges &amp; Applicators &#8211; Ziba</title>
    <meta name="robots" content="max-image-preview:large" />
    <script type="rocketlazyloadscript">
      window._wca = window._wca || [];
    </script>
    <link rel="dns-prefetch" href="//stats.wp.com" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com" />
    <link
      rel="alternate"
      type="application/rss+xml"
      title="Ziba &raquo; Feed"
      href="https://zibabeauty.in/index.php/feed/"
    />
    <link
      rel="alternate"
      type="application/rss+xml"
      title="Ziba &raquo; Comments Feed"
      href="https://zibabeauty.in/index.php/comments/feed/"
    />
    <link
      rel="alternate"
      type="application/rss+xml"
      title="Ziba &raquo; Sponges &amp; Applicators Category Feed"
      href="https://zibabeauty.in/index.php/product-category/sponges-applicators/feed/"
    />
    <script type="rocketlazyloadscript" data-rocket-type="text/javascript">
      /* <![CDATA[ */
      window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/15.0.3\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/15.0.3\/svg\/","svgExt":".svg","source":{"concatemoji":"https:\/\/zibabeauty.in\/wp-includes\/js\/wp-emoji-release.min.js?ver=6.6.2"}};
      /*! This file is auto-generated */
      !function(i,n){var o,s,e;function c(e){try{var t={supportTests:e,timestamp:(new Date).valueOf()};sessionStorage.setItem(o,JSON.stringify(t))}catch(e){}}function p(e,t,n){e.clearRect(0,0,e.canvas.width,e.canvas.height),e.fillText(t,0,0);var t=new Uint32Array(e.getImageData(0,0,e.canvas.width,e.canvas.height).data),r=(e.clearRect(0,0,e.canvas.width,e.canvas.height),e.fillText(n,0,0),new Uint32Array(e.getImageData(0,0,e.canvas.width,e.canvas.height).data));return t.every(function(e,t){return e===r[t]})}function u(e,t,n){switch(t){case"flag":return n(e,"\ud83c\udff3\ufe0f\u200d\u26a7\ufe0f","\ud83c\udff3\ufe0f\u200b\u26a7\ufe0f")?!1:!n(e,"\ud83c\uddfa\ud83c\uddf3","\ud83c\uddfa\u200b\ud83c\uddf3")&&!n(e,"\ud83c\udff4\udb40\udc67\udb40\udc62\udb40\udc65\udb40\udc6e\udb40\udc67\udb40\udc7f","\ud83c\udff4\u200b\udb40\udc67\u200b\udb40\udc62\u200b\udb40\udc65\u200b\udb40\udc6e\u200b\udb40\udc67\u200b\udb40\udc7f");case"emoji":return!n(e,"\ud83d\udc26\u200d\u2b1b","\ud83d\udc26\u200b\u2b1b")}return!1}function f(e,t,n){var r="undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope?new OffscreenCanvas(300,150):i.createElement("canvas"),a=r.getContext("2d",{willReadFrequently:!0}),o=(a.textBaseline="top",a.font="600 32px Arial",{});return e.forEach(function(e){o[e]=t(a,e,n)}),o}function t(e){var t=i.createElement("script");t.src=e,t.defer=!0,i.head.appendChild(t)}"undefined"!=typeof Promise&&(o="wpEmojiSettingsSupports",s=["flag","emoji"],n.supports={everything:!0,everythingExceptFlag:!0},e=new Promise(function(e){i.addEventListener("DOMContentLoaded",e,{once:!0})}),new Promise(function(t){var n=function(){try{var e=JSON.parse(sessionStorage.getItem(o));if("object"==typeof e&&"number"==typeof e.timestamp&&(new Date).valueOf()<e.timestamp+604800&&"object"==typeof e.supportTests)return e.supportTests}catch(e){}return null}();if(!n){if("undefined"!=typeof Worker&&"undefined"!=typeof OffscreenCanvas&&"undefined"!=typeof URL&&URL.createObjectURL&&"undefined"!=typeof Blob)try{var e="postMessage("+f.toString()+"("+[JSON.stringify(s),u.toString(),p.toString()].join(",")+"));",r=new Blob([e],{type:"text/javascript"}),a=new Worker(URL.createObjectURL(r),{name:"wpTestEmojiSupports"});return void(a.onmessage=function(e){c(n=e.data),a.terminate(),t(n)})}catch(e){}c(n=f(s,u,p))}t(n)}).then(function(e){for(var t in e)n.supports[t]=e[t],n.supports.everything=n.supports.everything&&n.supports[t],"flag"!==t&&(n.supports.everythingExceptFlag=n.supports.everythingExceptFlag&&n.supports[t]);n.supports.everythingExceptFlag=n.supports.everythingExceptFlag&&!n.supports.flag,n.DOMReady=!1,n.readyCallback=function(){n.DOMReady=!0}}).then(function(){return e}).then(function(){var e;n.supports.everything||(n.readyCallback(),(e=n.source||{}).concatemoji?t(e.concatemoji):e.wpemoji&&e.twemoji&&(t(e.twemoji),t(e.wpemoji)))}))}((window,document),window._wpemojiSettings);
      /* ]]> */
    </script>
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof-sd-switcher23-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/elements/switcher.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="wp-emoji-styles-inline-css" type="text/css">
      img.wp-smiley,
      img.emoji {
        display: inline !important;
        border: none !important;
        box-shadow: none !important;
        height: 1em !important;
        width: 1em !important;
        margin: 0 0.07em !important;
        vertical-align: -0.1em !important;
        background: none !important;
        padding: 0 !important;
      }
    </style>
    <link
      rel="stylesheet"
      id="wp-block-library-css"
      href="https://zibabeauty.in/wp-includes/css/dist/block-library/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <style id="wp-block-library-inline-css" type="text/css">
      .has-text-align-justify {
        text-align: justify;
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="qligg-swiper-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/insta-gallery/assets/frontend/swiper/swiper.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="qligg-frontend-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/insta-gallery/build/frontend/css/style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="buttons-css"
      href="https://zibabeauty.in/wp-includes/css/buttons.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="dashicons-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-includes/css/dashicons.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="dashicons-inline-css" type="text/css">
      [data-font="Dashicons"]:before {
        font-family: "Dashicons" !important;
        content: attr(data-icon) !important;
        speak: none !important;
        font-weight: normal !important;
        font-variant: normal !important;
        text-transform: none !important;
        line-height: 1 !important;
        font-style: normal !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
      }
    </style>
    <link
      rel="stylesheet"
      id="mediaelement-css"
      href="https://zibabeauty.in/wp-includes/js/mediaelement/mediaelementplayer-legacy.min.css?ver=4.2.17"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-mediaelement-css"
      href="https://zibabeauty.in/wp-includes/js/mediaelement/wp-mediaelement.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="media-views-css"
      href="https://zibabeauty.in/wp-includes/css/media-views.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-components-css"
      href="https://zibabeauty.in/wp-includes/css/dist/components/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-preferences-css"
      href="https://zibabeauty.in/wp-includes/css/dist/preferences/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-block-editor-css"
      href="https://zibabeauty.in/wp-includes/css/dist/block-editor/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-reusable-blocks-css"
      href="https://zibabeauty.in/wp-includes/css/dist/reusable-blocks/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-patterns-css"
      href="https://zibabeauty.in/wp-includes/css/dist/patterns/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="wp-editor-css"
      href="https://zibabeauty.in/wp-includes/css/dist/editor/style.min.css?ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="qligg-backend-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/insta-gallery/build/backend/css/style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="jetpack-videopress-video-block-view-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/jetpack/jetpack_vendor/automattic/jetpack-videopress/build/block-editor/blocks/video/view.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="wc-blocks-vendors-style-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce/packages/woocommerce-blocks/build/wc-blocks-vendors-style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="wc-blocks-style-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce/packages/woocommerce-blocks/build/wc-blocks-style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="joinchat-button-style-inline-css" type="text/css">
      .wp-block-joinchat-button {
        border: none !important;
        text-align: center;
      }
      .wp-block-joinchat-button figure {
        display: table;
        margin: 0 auto;
        padding: 0;
      }
      .wp-block-joinchat-button figcaption {
        font: normal normal 400 0.6em/2em
          var(--wp--preset--font-family--system-font, sans-serif);
        margin: 0;
        padding: 0;
      }
      .wp-block-joinchat-button .joinchat-button__qr {
        background-color: #fff;
        border: 6px solid #25d366;
        border-radius: 30px;
        box-sizing: content-box;
        display: block;
        height: 200px;
        margin: auto;
        overflow: hidden;
        padding: 10px;
        width: 200px;
      }
      .wp-block-joinchat-button .joinchat-button__qr canvas,
      .wp-block-joinchat-button .joinchat-button__qr img {
        display: block;
        margin: auto;
      }
      .wp-block-joinchat-button .joinchat-button__link {
        align-items: center;
        background-color: #25d366;
        border: 6px solid #25d366;
        border-radius: 30px;
        display: inline-flex;
        flex-flow: row nowrap;
        justify-content: center;
        line-height: 1.25em;
        margin: 0 auto;
        text-decoration: none;
      }
      .wp-block-joinchat-button .joinchat-button__link:before {
        background: transparent var(--joinchat-ico) no-repeat center;
        background-size: 100%;
        content: "";
        display: block;
        height: 1.5em;
        margin: -0.75em 0.75em -0.75em 0;
        width: 1.5em;
      }
      .wp-block-joinchat-button figure + .joinchat-button__link {
        margin-top: 10px;
      }
      @media (orientation: landscape) and (min-height: 481px),
        (orientation: portrait) and (min-width: 481px) {
        .wp-block-joinchat-button.joinchat-button--qr-only
          figure
          + .joinchat-button__link {
          display: none;
        }
      }
      @media (max-width: 480px),
        (orientation: landscape) and (max-height: 480px) {
        .wp-block-joinchat-button figure {
          display: none;
        }
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="jquery-selectBox-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/yith-woocommerce-wishlist/assets/css/jquery.selectBox.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="yith-wcwl-font-awesome-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/yith-woocommerce-wishlist/assets/css/font-awesome.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woocommerce_prettyPhoto_css-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce/assets/css/prettyPhoto.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="yith-wcwl-main-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/yith-woocommerce-wishlist/assets/css/style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="classic-theme-styles-inline-css" type="text/css">
      /*! This file is auto-generated */
      .wp-block-button__link {
        color: #fff;
        background-color: #32373c;
        border-radius: 9999px;
        box-shadow: none;
        text-decoration: none;
        padding: calc(0.667em + 2px) calc(1.333em + 2px);
        font-size: 1.125em;
      }
      .wp-block-file__button {
        background: #32373c;
        color: #fff;
        text-decoration: none;
      }
    </style>
    <style id="global-styles-inline-css" type="text/css">
      :root {
        --wp--preset--aspect-ratio--square: 1;
        --wp--preset--aspect-ratio--4-3: 4/3;
        --wp--preset--aspect-ratio--3-4: 3/4;
        --wp--preset--aspect-ratio--3-2: 3/2;
        --wp--preset--aspect-ratio--2-3: 2/3;
        --wp--preset--aspect-ratio--16-9: 16/9;
        --wp--preset--aspect-ratio--9-16: 9/16;
        --wp--preset--color--black: #000000;
        --wp--preset--color--cyan-bluish-gray: #abb8c3;
        --wp--preset--color--white: #ffffff;
        --wp--preset--color--pale-pink: #f78da7;
        --wp--preset--color--vivid-red: #cf2e2e;
        --wp--preset--color--luminous-vivid-orange: #ff6900;
        --wp--preset--color--luminous-vivid-amber: #fcb900;
        --wp--preset--color--light-green-cyan: #7bdcb5;
        --wp--preset--color--vivid-green-cyan: #00d084;
        --wp--preset--color--pale-cyan-blue: #8ed1fc;
        --wp--preset--color--vivid-cyan-blue: #0693e3;
        --wp--preset--color--vivid-purple: #9b51e0;
        --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(
          135deg,
          rgba(6, 147, 227, 1) 0%,
          rgb(155, 81, 224) 100%
        );
        --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(
          135deg,
          rgb(122, 220, 180) 0%,
          rgb(0, 208, 130) 100%
        );
        --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(
          135deg,
          rgba(252, 185, 0, 1) 0%,
          rgba(255, 105, 0, 1) 100%
        );
        --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(
          135deg,
          rgba(255, 105, 0, 1) 0%,
          rgb(207, 46, 46) 100%
        );
        --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(
          135deg,
          rgb(238, 238, 238) 0%,
          rgb(169, 184, 195) 100%
        );
        --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(
          135deg,
          rgb(74, 234, 220) 0%,
          rgb(151, 120, 209) 20%,
          rgb(207, 42, 186) 40%,
          rgb(238, 44, 130) 60%,
          rgb(251, 105, 98) 80%,
          rgb(254, 248, 76) 100%
        );
        --wp--preset--gradient--blush-light-purple: linear-gradient(
          135deg,
          rgb(255, 206, 236) 0%,
          rgb(152, 150, 240) 100%
        );
        --wp--preset--gradient--blush-bordeaux: linear-gradient(
          135deg,
          rgb(254, 205, 165) 0%,
          rgb(254, 45, 45) 50%,
          rgb(107, 0, 62) 100%
        );
        --wp--preset--gradient--luminous-dusk: linear-gradient(
          135deg,
          rgb(255, 203, 112) 0%,
          rgb(199, 81, 192) 50%,
          rgb(65, 88, 208) 100%
        );
        --wp--preset--gradient--pale-ocean: linear-gradient(
          135deg,
          rgb(255, 245, 203) 0%,
          rgb(182, 227, 212) 50%,
          rgb(51, 167, 181) 100%
        );
        --wp--preset--gradient--electric-grass: linear-gradient(
          135deg,
          rgb(202, 248, 128) 0%,
          rgb(113, 206, 126) 100%
        );
        --wp--preset--gradient--midnight: linear-gradient(
          135deg,
          rgb(2, 3, 129) 0%,
          rgb(40, 116, 252) 100%
        );
        --wp--preset--font-size--small: 13px;
        --wp--preset--font-size--medium: 20px;
        --wp--preset--font-size--large: 36px;
        --wp--preset--font-size--x-large: 42px;
        --wp--preset--spacing--20: 0.44rem;
        --wp--preset--spacing--30: 0.67rem;
        --wp--preset--spacing--40: 1rem;
        --wp--preset--spacing--50: 1.5rem;
        --wp--preset--spacing--60: 2.25rem;
        --wp--preset--spacing--70: 3.38rem;
        --wp--preset--spacing--80: 5.06rem;
        --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
        --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
        --wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
        --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1),
          6px 6px rgba(0, 0, 0, 1);
        --wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);
      }
      :where(.is-layout-flex) {
        gap: 0.5em;
      }
      :where(.is-layout-grid) {
        gap: 0.5em;
      }
      body .is-layout-flex {
        display: flex;
      }
      .is-layout-flex {
        flex-wrap: wrap;
        align-items: center;
      }
      .is-layout-flex > :is(*, div) {
        margin: 0;
      }
      body .is-layout-grid {
        display: grid;
      }
      .is-layout-grid > :is(*, div) {
        margin: 0;
      }
      :where(.wp-block-columns.is-layout-flex) {
        gap: 2em;
      }
      :where(.wp-block-columns.is-layout-grid) {
        gap: 2em;
      }
      :where(.wp-block-post-template.is-layout-flex) {
        gap: 1.25em;
      }
      :where(.wp-block-post-template.is-layout-grid) {
        gap: 1.25em;
      }
      .has-black-color {
        color: var(--wp--preset--color--black) !important;
      }
      .has-cyan-bluish-gray-color {
        color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }
      .has-white-color {
        color: var(--wp--preset--color--white) !important;
      }
      .has-pale-pink-color {
        color: var(--wp--preset--color--pale-pink) !important;
      }
      .has-vivid-red-color {
        color: var(--wp--preset--color--vivid-red) !important;
      }
      .has-luminous-vivid-orange-color {
        color: var(--wp--preset--color--luminous-vivid-orange) !important;
      }
      .has-luminous-vivid-amber-color {
        color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }
      .has-light-green-cyan-color {
        color: var(--wp--preset--color--light-green-cyan) !important;
      }
      .has-vivid-green-cyan-color {
        color: var(--wp--preset--color--vivid-green-cyan) !important;
      }
      .has-pale-cyan-blue-color {
        color: var(--wp--preset--color--pale-cyan-blue) !important;
      }
      .has-vivid-cyan-blue-color {
        color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }
      .has-vivid-purple-color {
        color: var(--wp--preset--color--vivid-purple) !important;
      }
      .has-black-background-color {
        background-color: var(--wp--preset--color--black) !important;
      }
      .has-cyan-bluish-gray-background-color {
        background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }
      .has-white-background-color {
        background-color: var(--wp--preset--color--white) !important;
      }
      .has-pale-pink-background-color {
        background-color: var(--wp--preset--color--pale-pink) !important;
      }
      .has-vivid-red-background-color {
        background-color: var(--wp--preset--color--vivid-red) !important;
      }
      .has-luminous-vivid-orange-background-color {
        background-color: var(
          --wp--preset--color--luminous-vivid-orange
        ) !important;
      }
      .has-luminous-vivid-amber-background-color {
        background-color: var(
          --wp--preset--color--luminous-vivid-amber
        ) !important;
      }
      .has-light-green-cyan-background-color {
        background-color: var(--wp--preset--color--light-green-cyan) !important;
      }
      .has-vivid-green-cyan-background-color {
        background-color: var(--wp--preset--color--vivid-green-cyan) !important;
      }
      .has-pale-cyan-blue-background-color {
        background-color: var(--wp--preset--color--pale-cyan-blue) !important;
      }
      .has-vivid-cyan-blue-background-color {
        background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }
      .has-vivid-purple-background-color {
        background-color: var(--wp--preset--color--vivid-purple) !important;
      }
      .has-black-border-color {
        border-color: var(--wp--preset--color--black) !important;
      }
      .has-cyan-bluish-gray-border-color {
        border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }
      .has-white-border-color {
        border-color: var(--wp--preset--color--white) !important;
      }
      .has-pale-pink-border-color {
        border-color: var(--wp--preset--color--pale-pink) !important;
      }
      .has-vivid-red-border-color {
        border-color: var(--wp--preset--color--vivid-red) !important;
      }
      .has-luminous-vivid-orange-border-color {
        border-color: var(
          --wp--preset--color--luminous-vivid-orange
        ) !important;
      }
      .has-luminous-vivid-amber-border-color {
        border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }
      .has-light-green-cyan-border-color {
        border-color: var(--wp--preset--color--light-green-cyan) !important;
      }
      .has-vivid-green-cyan-border-color {
        border-color: var(--wp--preset--color--vivid-green-cyan) !important;
      }
      .has-pale-cyan-blue-border-color {
        border-color: var(--wp--preset--color--pale-cyan-blue) !important;
      }
      .has-vivid-cyan-blue-border-color {
        border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }
      .has-vivid-purple-border-color {
        border-color: var(--wp--preset--color--vivid-purple) !important;
      }
      .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
        background: var(
          --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple
        ) !important;
      }
      .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
        background: var(
          --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan
        ) !important;
      }
      .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
        background: var(
          --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange
        ) !important;
      }
      .has-luminous-vivid-orange-to-vivid-red-gradient-background {
        background: var(
          --wp--preset--gradient--luminous-vivid-orange-to-vivid-red
        ) !important;
      }
      .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
        background: var(
          --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray
        ) !important;
      }
      .has-cool-to-warm-spectrum-gradient-background {
        background: var(
          --wp--preset--gradient--cool-to-warm-spectrum
        ) !important;
      }
      .has-blush-light-purple-gradient-background {
        background: var(--wp--preset--gradient--blush-light-purple) !important;
      }
      .has-blush-bordeaux-gradient-background {
        background: var(--wp--preset--gradient--blush-bordeaux) !important;
      }
      .has-luminous-dusk-gradient-background {
        background: var(--wp--preset--gradient--luminous-dusk) !important;
      }
      .has-pale-ocean-gradient-background {
        background: var(--wp--preset--gradient--pale-ocean) !important;
      }
      .has-electric-grass-gradient-background {
        background: var(--wp--preset--gradient--electric-grass) !important;
      }
      .has-midnight-gradient-background {
        background: var(--wp--preset--gradient--midnight) !important;
      }
      .has-small-font-size {
        font-size: var(--wp--preset--font-size--small) !important;
      }
      .has-medium-font-size {
        font-size: var(--wp--preset--font-size--medium) !important;
      }
      .has-large-font-size {
        font-size: var(--wp--preset--font-size--large) !important;
      }
      .has-x-large-font-size {
        font-size: var(--wp--preset--font-size--x-large) !important;
      }
      :where(.wp-block-post-template.is-layout-flex) {
        gap: 1.25em;
      }
      :where(.wp-block-post-template.is-layout-grid) {
        gap: 1.25em;
      }
      :where(.wp-block-columns.is-layout-flex) {
        gap: 2em;
      }
      :where(.wp-block-columns.is-layout-grid) {
        gap: 2em;
      }
      :root :where(.wp-block-pullquote) {
        font-size: 1.5em;
        line-height: 1.6;
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="redux-extendify-styles-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/redux-framework/redux-core/assets/css/extendify-utilities.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/css/front.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="woof-inline-css" type="text/css">
      .woof_products_top_panel li span,
      .woof_products_top_panel2 li span {
        background: url(https://zibabeauty.in/wp-content/plugins/woocommerce-products-filter/img/delete.png);
        background-size: 14px 14px;
        background-repeat: no-repeat;
        background-position: right;
      }
      .woof_edit_view {
        display: none;
      }
      .woof_price_search_container .price_slider_amount button.button {
        display: none;
      }

      /***** END: hiding submit button of the price slider ******/
    </style>
    <link
      rel="stylesheet"
      id="chosen-drop-down-css"
      href="https://zibabeauty.in/wp-content/plugins/woocommerce-products-filter/js/chosen/chosen.min.css?ver=3.3.2"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="icheck-jquery-color-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/js/icheck/skins/square/blue.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_by_onsales_html_items-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/by_onsales/css/by_onsales.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_by_text_html_items-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/by_text/assets/css/front.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_label_html_items-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/label/css/html_types/label.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_sd_html_items_checkbox-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/elements/checkbox.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_sd_html_items_radio-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/elements/radio.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_sd_html_items_switcher-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/elements/switcher.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_sd_html_items_color-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/elements/color.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_sd_html_items_tooltip-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/tooltip.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woof_sd_html_items_front-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/smart_designer/css/front.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woocommerce-layout-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="woocommerce-layout-inline-css" type="text/css">
      .infinite-scroll .woocommerce-pagination {
        display: none;
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="woocommerce-smallscreen-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=1717589898"
      type="text/css"
      media="only screen and (max-width: 768px)"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="woocommerce-general-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="woocommerce-inline-inline-css" type="text/css">
      .woocommerce form .form-row .required {
        visibility: visible;
      }
    </style>
    <link
      rel="stylesheet"
      id="woo-variation-swatches-css"
      href="https://zibabeauty.in/wp-content/plugins/woo-variation-swatches/assets/css/frontend.min.css?ver=1717589900"
      type="text/css"
      media="all"
    />
    <style id="woo-variation-swatches-inline-css" type="text/css">
      :root {
        --wvs-tick: url("data:image/svg+xml;utf8,%3Csvg filter='drop-shadow(0px 0px 2px rgb(0 0 0 / .8))' xmlns='http://www.w3.org/2000/svg'  viewBox='0 0 30 30'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='4' d='M4 16L11 23 27 7'/%3E%3C/svg%3E");

        --wvs-cross: url("data:image/svg+xml;utf8,%3Csvg filter='drop-shadow(0px 0px 5px rgb(255 255 255 / .6))' xmlns='http://www.w3.org/2000/svg' width='72px' height='72px' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='%23ff0000' stroke-linecap='round' stroke-width='0.6' d='M5 5L19 19M19 5L5 19'/%3E%3C/svg%3E");
        --wvs-single-product-item-width: 30px;
        --wvs-single-product-item-height: 30px;
        --wvs-single-product-item-font-size: 16px;
      }
    </style>
    <link
      rel="stylesheet"
      id="hara-theme-fonts-css"
      href="https://fonts.googleapis.com/css?family=Inter%3A400%2C500%2C600%2C700%7CCormorant%20Garamond%3A400%2C500%2C600%2C700&#038;subset=latin%2Clatin-ext&#038;display=swap"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="joinchat-css"
      href="https://zibabeauty.in/wp-content/plugins/creame-whatsapp-me/public/css/joinchat-btn.min.css?ver=4.5.20"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="bootstrap-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/bootstrap.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-frontend-css"
      href="https://zibabeauty.in/wp-content/plugins/elementor/assets/css/frontend-lite.min.css?ver=3.14.1"
      type="text/css"
      media="all"
    />
    <style id="elementor-frontend-inline-css" type="text/css">
      .elementor-kit-6 {
        --e-global-color-primary: #6ec1e4;
        --e-global-color-secondary: #54595f;
        --e-global-color-text: #7a7a7a;
        --e-global-color-accent: #61ce70;
        --e-global-typography-primary-font-family: "Roboto";
        --e-global-typography-primary-font-weight: 600;
        --e-global-typography-secondary-font-family: "Roboto Slab";
        --e-global-typography-secondary-font-weight: 400;
        --e-global-typography-text-font-family: "Roboto";
        --e-global-typography-text-font-weight: 400;
        --e-global-typography-accent-font-family: "Roboto";
        --e-global-typography-accent-font-weight: 500;
      }
      .elementor-section.elementor-section-boxed > .elementor-container {
        max-width: 1356px !important;
      }
      .elementor-widget:not(:last-child) {
        margin-bottom: 20px;
      }
      .elementor-element {
        --widgets-spacing: 20px;
      }
       {
      }
      h1.page-title {
        display: var(--page-title-display);
      }
      @media (max-width: 1024px) {
        .elementor-section.elementor-section-boxed > .elementor-container {
          max-width: 1024px !important;
        }
      }
      @media (max-width: 767px) {
        .elementor-section.elementor-section-boxed > .elementor-container {
          max-width: 767px !important;
        }
      }
      .elementor-51
        .elementor-element.elementor-element-c92871a:not(.elementor-motion-effects-element-type-background),
      .elementor-51
        .elementor-element.elementor-element-c92871a
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #1d1c1c;
      }
      .elementor-51 .elementor-element.elementor-element-c92871a {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        margin-top: 0px;
        margin-bottom: 0px;
        padding: 6px 6px 6px 6px;
      }
      .elementor-51
        .elementor-element.elementor-element-c92871a
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-51 .elementor-element.elementor-element-8995130 {
        text-align: center;
      }
      .elementor-51
        .elementor-element.elementor-element-8995130
        .elementor-heading-title {
        color: #ffffff;
        font-size: 16px;
      }
      .elementor-51
        .elementor-element.elementor-element-73fc734
        > .elementor-container
        > .elementor-column
        > .elementor-widget-wrap {
        align-content: center;
        align-items: center;
      }
      .elementor-51
        .elementor-element.elementor-element-73fc734:not(.elementor-motion-effects-element-type-background),
      .elementor-51
        .elementor-element.elementor-element-73fc734
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-color: #2c2c2c;
      }
      .elementor-51 .elementor-element.elementor-element-73fc734 {
        border-style: solid;
        border-width: 0px 0px 1px 0px;
        border-color: #ffffff2e;
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
        padding: 16px 0px 15px 0px;
      }
      .elementor-51
        .elementor-element.elementor-element-73fc734
        > .elementor-background-overlay {
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-51 .elementor-element.elementor-element-9d6c1d4 img {
        max-width: 148px;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu {
        justify-content: center !important;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-horizontal
        .navbar-nav
        > li
        > a:after {
        display: none !important;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > .caret:before {
        background-color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:hover,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li:hover
        > a
        > .caret,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li:focus
        > a
        > .caret,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current-menu-parent
        > a
        > .caret,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:hover
        i,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:focus
        i,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a.active
        i,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li
        > a:focus,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current-menu-parent
        > a,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .tbay-element-nav-menu
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current-menu-item
        > a,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current_page_item
        > a,
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main.tbay-horizontal
        > ul
        > li.current_page_parent
        > a {
        color: #ffffff !important;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main
        .elementor-item {
        padding: 26px 0px 26px 0px;
      }
      .elementor-51
        .elementor-element.elementor-element-43cd067
        .elementor-nav-menu--main
        .dropdown-menu
        .elementor-item {
        padding: 0;
      }
      .elementor-51 .elementor-element.elementor-element-43cd067 {
        width: var(--container-widget-width, 106.424%);
        max-width: 106.424%;
        --container-widget-width: 106.424%;
        --container-widget-flex-grow: 0;
      }
      .elementor-51
        .elementor-element.elementor-element-e6161df
        > div.elementor-element-populated {
        padding: 0px 0px 0px 18px !important;
      }
      .elementor-51
        .elementor-element.elementor-element-b54d16e.elementor-column
        > .elementor-widget-wrap {
        justify-content: flex-end;
      }
      .elementor-51
        .elementor-element.elementor-element-b54d16e
        > div.elementor-element-populated {
        padding: 0px 18px 0px 0px !important;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-dropdown
        .cart-icon {
        color: #ffffff;
        background-color: #02010100;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-dropdown
        .cart-icon:hover {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-icon
        span.mini-cart-items {
        font-size: 12px;
        font-weight: 400;
      }
      .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-popup
        .dropdown-menu.show {
        inset: 51px 0px auto auto !important;
      }
      .rtl
        .elementor-51
        .elementor-element.elementor-element-74a6cf1
        .cart-popup
        .dropdown-menu.show {
        inset: 51px auto auto 0px !important;
      }
      .elementor-51
        .elementor-element.elementor-element-41757a2
        .tbay-login
        a
        i {
        font-size: 20px !important;
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-41757a2
        .tbay-login
        > a:hover
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-baae983
        .top-wishlist
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-baae983
        .top-wishlist
        a:hover
        i {
        color: #ffffff;
      }
      .elementor-51
        .elementor-element.elementor-element-baae983
        .top-wishlist
        .count_wishlist {
        font-size: 12px;
        font-weight: 400;
      }
      @media (min-width: 768px) {
        .elementor-51 .elementor-element.elementor-element-b88b4aa {
          width: 19.912%;
        }
        .elementor-51 .elementor-element.elementor-element-a61c64d {
          width: 57.749%;
        }
        .elementor-51 .elementor-element.elementor-element-e6161df {
          width: 5.149%;
        }
        .elementor-51 .elementor-element.elementor-element-b54d16e {
          width: 17.156%;
        }
      }
      .elementor-939
        .elementor-element.elementor-element-63862f22:not(.elementor-motion-effects-element-type-background),
      .elementor-939
        .elementor-element.elementor-element-63862f22
        > .elementor-motion-effects-container
        > .elementor-motion-effects-layer {
        background-image: url("https://zibabeauty.in/wp-content/uploads/2023/04/overhead-view-beauty-products-professional-make-up-concrete-background-scaled-1.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      .elementor-939
        .elementor-element.elementor-element-63862f22
        > .elementor-background-overlay {
        background-color: #000000;
        opacity: 0.84;
        transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
      }
      .elementor-939 .elementor-element.elementor-element-63862f22 {
        transition: background 0.3s, border 0.3s, border-radius 0.3s,
          box-shadow 0.3s;
      }
      .elementor-939
        .elementor-element.elementor-element-483f4d2c
        > .elementor-element-populated {
        margin: 30px 0px 0px 22px;
        --e-column-margin-right: 0px;
        --e-column-margin-left: 22px;
      }
      .elementor-939 .elementor-element.elementor-element-51f49c3a img {
        width: 38%;
      }
      .elementor-939 .elementor-element.elementor-element-d4664c1 {
        --spacer-size: 30px;
      }
      .elementor-939 .elementor-element.elementor-element-3f100d1d {
        --grid-template-columns: repeat(4, auto);
        --icon-size: 25px;
        --grid-column-gap: 8px;
        --grid-row-gap: 0px;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-widget-container {
        text-align: center;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon {
        background-color: #02010100;
        --icon-padding: 0.3em;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon
        i {
        color: #ffffff;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon
        svg {
        fill: #ffffff;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon:hover
        i {
        color: #b0aaaa;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        .elementor-social-icon:hover
        svg {
        fill: #b0aaaa;
      }
      .elementor-939
        .elementor-element.elementor-element-3f100d1d
        > .elementor-widget-container {
        padding: 0px 0px 0px 0px;
        border-style: solid;
        border-width: 0px 0px 0px 0px;
        border-color: #919eab3d;
      }
      .elementor-939 .elementor-element.elementor-element-4a94fdf {
        --spacer-size: 30px;
      }
      .elementor-939 .elementor-element.elementor-element-230a799 {
        text-align: center;
        color: #ffffff;
      }
      .elementor-939 .elementor-element.elementor-element-51bbe304 {
        text-align: center;
      }
      .elementor-939
        .elementor-element.elementor-element-51bbe304
        .elementor-heading-title {
        color: #ffffff;
      }
      .elementor-939
        .elementor-element.elementor-element-51bbe304
        > .elementor-widget-container {
        margin: 5px 0px 0px 0px;
        padding: 10px 0px 0px 0px;
      }
      .elementor-939 .elementor-element.elementor-element-32bc31dc {
        --spacer-size: 50px;
      }
      @media (max-width: 1024px) {
        .elementor-939
          .elementor-element.elementor-element-32a9ad65
          > .elementor-widget-container {
          margin: 0px 0px 0px 0px;
        }
        .elementor-939 .elementor-element.elementor-element-32a9ad65 {
          width: 100%;
          max-width: 100%;
        }
        .elementor-939
          .elementor-element.elementor-element-3f100d1d
          > .elementor-widget-container {
          border-width: 0px 0px 0px 0px;
        }
      }
      @media (max-width: 767px) {
        .elementor-939
          .elementor-element.elementor-element-63862f22:not(.elementor-motion-effects-element-type-background),
        .elementor-939
          .elementor-element.elementor-element-63862f22
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-size: cover;
        }
        .elementor-939 .elementor-element.elementor-element-51f49c3a {
          text-align: center;
        }
        .elementor-939
          .elementor-element.elementor-element-3f100d1d
          .elementor-widget-container {
          text-align: center;
        }
        .elementor-939
          .elementor-element.elementor-element-3f100d1d
          > .elementor-widget-container {
          margin: 0px 0px 32px 0px;
          padding: 26px 0px 32px 0px;
          border-width: 0px 0px 1px 0px;
        }
        .elementor-939
          .elementor-element.elementor-element-230a799
          > .elementor-widget-container {
          margin: 22px 0px 0px 0px;
        }
      }
      @media (min-width: 768px) {
        .elementor-939 .elementor-element.elementor-element-483f4d2c {
          width: 30.767%;
        }
        .elementor-939 .elementor-element.elementor-element-739b6f5a {
          width: 38.247%;
        }
        .elementor-939 .elementor-element.elementor-element-158fcf35 {
          width: 30.652%;
        }
      }
      @media (min-width: 1025px) {
        .elementor-939
          .elementor-element.elementor-element-63862f22:not(.elementor-motion-effects-element-type-background),
        .elementor-939
          .elementor-element.elementor-element-63862f22
          > .elementor-motion-effects-container
          > .elementor-motion-effects-layer {
          background-attachment: scroll;
        }
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="hara-template-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/template.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="hara-style-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/style.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="hara-style-inline-css" type="text/css">
      :root {
        --tb-theme-color: #2c2c2c;
        --tb-theme-color-hover: #292929;
        --tb-header-mobile-bg: #2c2c2c;
        --tb-back-to-top-bg: #fff;
        --tb-back-to-top-bg-hover: #c4743f;
        --tb-back-to-top-color: #ffffff;
        --tb-back-to-top-color-hover: #fff;
        --tb-header-mobile-color: #ffffff;
      }
      :root {
        --tb-text-primary-font: Inter, sans-serif;
        --tb-text-second-font: Cormorant Garamond, sans-serif;
      } /* Theme Options Styles */
      .checkout-logo img {
        max-width: 120px;
      }
      @media (max-width: 1199px) {
        /* Limit logo image height for mobile according to mobile header height */
        .mobile-logo a img {
          width: 100px;
        }
        .mobile-logo a img {
          padding-top: 5px;
        }
      }
      @media screen and (max-width: 782px) {
        html body.admin-bar {
          top: -46px !important;
          position: relative;
        }
      } /* Custom CSS */
      @media screen and (max-width: 767px) {
        body #message-purchased {
          display: none !important;
        }
      }
      @media (min-width: 1200px) {
        .slick-vertical div.slick-list {
          min-height: 304px !important;
        }
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="elementor-icons-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/eicons/css/elementor-icons.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="swiper-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="font-awesome-5-all-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/all.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="font-awesome-4-shim-css"
      href="https://zibabeauty.in/wp-content/plugins/elementor/assets/lib/font-awesome/css/v4-shims.min.css?ver=3.14.1"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="font-awesome-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <style id="font-awesome-inline-css" type="text/css">
      [data-font="FontAwesome"]:before {
        font-family: "FontAwesome" !important;
        content: attr(data-icon) !important;
        speak: none !important;
        font-weight: normal !important;
        font-variant: normal !important;
        text-transform: none !important;
        line-height: 1 !important;
        font-style: normal !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
      }
    </style>
    <link
      data-minify="1"
      rel="stylesheet"
      id="hara-font-tbay-custom-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/font-tbay-custom.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="simple-line-icons-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/simple-line-icons.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="material-design-iconic-font-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/material-design-iconic-font.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="animate-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/animate.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="jquery-treeview-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/jquery.treeview.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="magnific-popup-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/themes/hara/css/magnific-popup.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hara-child-style-css"
      href="https://zibabeauty.in/wp-content/themes/hara-child/style.css?ver=1.1.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="google-fonts-1-css"
      href="https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.6.2"
      type="text/css"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-icons-shared-0-css"
      href="https://zibabeauty.in/wp-content/plugins/elementor/assets/lib/font-awesome/css/fontawesome.min.css?ver=5.15.3"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="elementor-icons-fa-brands-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/elementor/assets/lib/font-awesome/css/brands.min.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link
      data-minify="1"
      rel="stylesheet"
      id="jetpack_css-css"
      href="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/jetpack/css/jetpack.css?ver=1717589898"
      type="text/css"
      media="all"
    />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <script type="text/template" id="tmpl-unavailable-variation-template">
      <p>Sorry, this product is unavailable. Please choose a different combination.</p>
    </script>
    <script type="text/javascript" id="woof-husky-js-extra">
      /* <![CDATA[ */
      var woof_husky_txt = {
        ajax_url: "https:\/\/zibabeauty.in\/wp-admin\/admin-ajax.php",
        plugin_uri:
          "https:\/\/zibabeauty.in\/wp-content\/plugins\/woocommerce-products-filter\/ext\/by_text\/",
        loader:
          "https:\/\/zibabeauty.in\/wp-content\/plugins\/woocommerce-products-filter\/ext\/by_text\/assets\/img\/ajax-loader.gif",
        not_found: "Nothing found!",
        prev: "Prev",
        next: "Next",
        site_link: "https:\/\/zibabeauty.in",
        default_data: {
          placeholder: "",
          behavior: "title_or_content_or_excerpt",
          search_by_full_word: "0",
          autocomplete: "1",
          how_to_open_links: "0",
          taxonomy_compatibility: "0",
          sku_compatibility: "1",
          custom_fields: "",
          search_desc_variant: "0",
          view_text_length: "10",
          min_symbols: "3",
          max_posts: "10",
          image: "",
          notes_for_customer: "",
          template: "",
          max_open_height: "300",
          page: 0,
        },
      };
      /* ]]> */
    </script>
    <script
      type="rocketlazyloadscript"
      data-minify="1"
      data-rocket-type="text/javascript"
      data-rocket-src="https://zibabeauty.in/wp-content/cache/min/1/wp-content/plugins/woocommerce-products-filter/ext/by_text/assets/js/husky.js?ver=1717589898"
      id="woof-husky-js"
      defer
    ></script>
    <script
      type="rocketlazyloadscript"
      data-rocket-type="text/javascript"
      data-rocket-src="https://zibabeauty.in/wp-includes/js/jquery/jquery.min.js?ver=3.7.1"
      id="jquery-core-js"
    ></script>
    <script
      type="rocketlazyloadscript"
      data-rocket-type="text/javascript"
      data-rocket-src="https://zibabeauty.in/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1"
      id="jquery-migrate-js"
    ></script>
    <script
      defer
      type="text/javascript"
      src="https://stats.wp.com/s-202515.js"
      id="woocommerce-analytics-js"
    ></script>
    <script
      type="rocketlazyloadscript"
      data-rocket-type="text/javascript"
      data-rocket-src="https://zibabeauty.in/wp-content/plugins/elementor/assets/lib/font-awesome/js/v4-shims.min.js?ver=3.14.1"
      id="font-awesome-4-shim-js"
      defer
    ></script>
    <link
      rel="https://api.w.org/"
      href="https://zibabeauty.in/index.php/wp-json/"
    />
    <link
      rel="alternate"
      title="JSON"
      type="application/json"
      href="https://zibabeauty.in/index.php/wp-json/wp/v2/product_cat/96"
    />
    <link
      rel="EditURI"
      type="application/rsd+xml"
      title="RSD"
      href="https://zibabeauty.in/xmlrpc.php?rsd"
    />
    <meta name="generator" content="WordPress 6.6.2" />
    <meta name="generator" content="WooCommerce 7.8.1" />
    <meta name="generator" content="Redux 4.4.4" />
    <style>
      img#wpstats {
        display: none;
      }
    </style>
    <noscript
      ><style>
        .woocommerce-product-gallery {
          opacity: 1 !important;
        }
      </style></noscript
    >
    <meta
      name="generator"
      content="Elementor 3.14.1; features: e_dom_optimization, e_optimized_assets_loading, e_optimized_css_loading, a11y_improvements, additional_custom_breakpoints; settings: css_print_method-internal, google_font-enabled, font_display-swap"
    />
    <meta
      name="generator"
      content="Powered by Slider Revolution 6.6.12 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface."
    />
    <script type="rocketlazyloadscript">
      function setREVStartSize(e){
      			//window.requestAnimationFrame(function() {
      				window.RSIW = window.RSIW===undefined ? window.innerWidth : window.RSIW;
      				window.RSIH = window.RSIH===undefined ? window.innerHeight : window.RSIH;
      				try {
      					var pw = document.getElementById(e.c).parentNode.offsetWidth,
      						newh;
      					pw = pw===0 || isNaN(pw) || (e.l=="fullwidth" || e.layout=="fullwidth") ? window.RSIW : pw;
      					e.tabw = e.tabw===undefined ? 0 : parseInt(e.tabw);
      					e.thumbw = e.thumbw===undefined ? 0 : parseInt(e.thumbw);
      					e.tabh = e.tabh===undefined ? 0 : parseInt(e.tabh);
      					e.thumbh = e.thumbh===undefined ? 0 : parseInt(e.thumbh);
      					e.tabhide = e.tabhide===undefined ? 0 : parseInt(e.tabhide);
      					e.thumbhide = e.thumbhide===undefined ? 0 : parseInt(e.thumbhide);
      					e.mh = e.mh===undefined || e.mh=="" || e.mh==="auto" ? 0 : parseInt(e.mh,0);
      					if(e.layout==="fullscreen" || e.l==="fullscreen")
      						newh = Math.max(e.mh,window.RSIH);
      					else{
      						e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
      						for (var i in e.rl) if (e.gw[i]===undefined || e.gw[i]===0) e.gw[i] = e.gw[i-1];
      						e.gh = e.el===undefined || e.el==="" || (Array.isArray(e.el) && e.el.length==0)? e.gh : e.el;
      						e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
      						for (var i in e.rl) if (e.gh[i]===undefined || e.gh[i]===0) e.gh[i] = e.gh[i-1];

      						var nl = new Array(e.rl.length),
      							ix = 0,
      							sl;
      						e.tabw = e.tabhide>=pw ? 0 : e.tabw;
      						e.thumbw = e.thumbhide>=pw ? 0 : e.thumbw;
      						e.tabh = e.tabhide>=pw ? 0 : e.tabh;
      						e.thumbh = e.thumbhide>=pw ? 0 : e.thumbh;
      						for (var i in e.rl) nl[i] = e.rl[i]<window.RSIW ? 0 : e.rl[i];
      						sl = nl[0];
      						for (var i in nl) if (sl>nl[i] && nl[i]>0) { sl = nl[i]; ix=i;}
      						var m = pw>(e.gw[ix]+e.tabw+e.thumbw) ? 1 : (pw-(e.tabw+e.thumbw)) / (e.gw[ix]);
      						newh =  (e.gh[ix] * m) + (e.tabh + e.thumbh);
      					}
      					var el = document.getElementById(e.c);
      					if (el!==null && el) el.style.height = newh+"px";
      					el = document.getElementById(e.c+"_wrapper");
      					if (el!==null && el) {
      						el.style.height = newh+"px";
      						el.style.display = "block";
      					}
      				} catch(e){
      					console.log("Failure at Presize of Slider:" + e)
      				}
      			//});
      		  };
    </script>
    <style type="text/css" id="wp-custom-css">
      .elementor-widget-tbay-nav-menu .tbay-horizontal .navbar-nav .sub-menu {
        background: #2c2c2c;
      }
      .elementor-widget-tbay-nav-menu .tbay-horizontal .navbar-nav .sub-menu a {
        color: white;
      }

      .menu li.current-menu-item a {
        color: white;
      }

      .menu li.current-menu-item a {
        color: #212529;
      }
      .elementor-widget-tbay-nav-menu
        .tbay-horizontal
        .navbar-nav
        > li.menu-item-has-children
        .sub-menu
        > li
        > a:hover {
        color: grey;
      }
    </style>
    <noscript
      ><style id="rocket-lazyload-nojs-css">
        .rll-youtube-player,
        [data-lazy-src] {
          display: none !important;
        }
      </style></noscript
    >
    <style id="wpforms-css-vars-root">
      :root {
        --wpforms-field-border-radius: 3px;
        --wpforms-field-background-color: #ffffff;
        --wpforms-field-border-color: rgba(0, 0, 0, 0.25);
        --wpforms-field-text-color: rgba(0, 0, 0, 0.7);
        --wpforms-label-color: rgba(0, 0, 0, 0.85);
        --wpforms-label-sublabel-color: rgba(0, 0, 0, 0.55);
        --wpforms-label-error-color: #d63637;
        --wpforms-button-border-radius: 3px;
        --wpforms-button-background-color: #066aab;
        --wpforms-button-text-color: #ffffff;
        --wpforms-field-size-input-height: 43px;
        --wpforms-field-size-input-spacing: 15px;
        --wpforms-field-size-font-size: 16px;
        --wpforms-field-size-line-height: 19px;
        --wpforms-field-size-padding-h: 14px;
        --wpforms-field-size-checkbox-size: 16px;
        --wpforms-field-size-sublabel-spacing: 5px;
        --wpforms-field-size-icon-size: 1;
        --wpforms-label-size-font-size: 16px;
        --wpforms-label-size-line-height: 19px;
        --wpforms-label-size-sublabel-font-size: 14px;
        --wpforms-label-size-sublabel-line-height: 17px;
        --wpforms-button-size-font-size: 17px;
        --wpforms-button-size-height: 41px;
        --wpforms-button-size-padding-h: 15px;
        --wpforms-button-size-margin-top: 10px;
      }
    </style>
  </head>
  <body
    class="archive tax-product_cat term-sponges-applicators term-96 theme-hara woocommerce woocommerce-page woocommerce-no-js woo-variation-swatches wvs-behavior-blur wvs-theme-hara-child wvs-show-label wvs-tooltip tbay-search-mb tbay-show-cart-mobile tbay-body-mobile-product-two elementor-default elementor-kit-6 woocommerce tbay-product-category tbay-variation-free ajax_cart_popup mobile-show-footer-desktop mobile-show-footer-icon"
  >
    <div id="wrapper-container" class="wrapper-container">
      <div
        id="tbay-mobile-smartmenu"
        data-title="Menu"
        class="tbay-mmenu d-xl-none"
      >
        <div class="tbay-offcanvas-body">
          <div id="mmenu-close">
            <button
              type="button"
              class="btn btn-toggle-canvas"
              data-toggle="offcanvas"
            >
              <i class="tb-icon tb-icon-close-01"></i>
            </button>
          </div>

          <nav
            id="tbay-mobile-menu-navbar"
            class="menu navbar navbar-offcanvas navbar-static"
            data-id="menu-mobile-new"
          >
            <div id="main-mobile-menu-mmenu" class="menu-mobile-new-container">
              <ul
                id="main-mobile-menu-mmenu-wrapper"
                class="menu"
                data-id="mobile-new"
              >
                <li
                  id="menu-item-6696"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home menu-item-6696"
                >
                  <a class="elementor-item" href="https://zibabeauty.in/"
                    ><span class="menu-title">Home</span></a
                  >
                </li>
                <li
                  id="menu-item-6697"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6697"
                >
                  <a
                    class="elementor-item"
                    href="https://zibabeauty.in/index.php/about-us/"
                    ><span class="menu-title">About Us</span></a
                  >
                </li>
                <li
                  id="menu-item-6695"
                  class="menu-item menu-item-type-custom menu-item-object-custom current-menu-ancestor current-menu-parent menu-item-has-children menu-item-6695"
                >
                  <a class="elementor-item" href="#"
                    ><span class="menu-title">Categories</span
                    ><b class="caret"></b
                  ></a>
                  <ul class="sub-menu">
                    <li
                      id="menu-item-6700"
                      class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6700"
                    >
                      <a
                        class="elementor-item"
                        href="https://zibabeauty.in/index.php/product-category/disposable-brushes/"
                        ><span class="menu-title">Disposable Brushes</span></a
                      >
                    </li>
                    <li
                      id="menu-item-6701"
                      class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-has-children menu-item-6701"
                    >
                      <a
                        class="elementor-item"
                        href="https://zibabeauty.in/index.php/product-category/makeup-brushes/"
                        ><span class="menu-title">Makeup Brushes</span
                        ><b class="caret"></b
                      ></a>
                      <ul class="sub-menu">
                        <li
                          id="menu-item-6702"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6702"
                        >
                          <a
                            class="elementor-item"
                            href="https://zibabeauty.in/index.php/product-category/makeup-brushes/brush-set/"
                            ><span class="menu-title">Brush Set</span></a
                          >
                        </li>
                        <li
                          id="menu-item-6703"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6703"
                        >
                          <a
                            class="elementor-item"
                            href="https://zibabeauty.in/index.php/product-category/makeup-brushes/classic-collection/"
                            ><span class="menu-title"
                              >Classic Collection</span
                            ></a
                          >
                        </li>
                        <li
                          id="menu-item-6704"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6704"
                        >
                          <a
                            class="elementor-item"
                            href="https://zibabeauty.in/index.php/product-category/makeup-brushes/metallic-collection/"
                            ><span class="menu-title"
                              >Metallic Collection</span
                            ></a
                          >
                        </li>
                        <li
                          id="menu-item-6705"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6705"
                        >
                          <a
                            class="elementor-item"
                            href="https://zibabeauty.in/index.php/product-category/makeup-brushes/professional-brushes/"
                            ><span class="menu-title"
                              >Professional Brushes</span
                            ></a
                          >
                        </li>
                        <li
                          id="menu-item-6706"
                          class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-6706"
                        >
                          <a
                            class="elementor-item"
                            href="https://zibabeauty.in/index.php/product-category/makeup-brushes/single-brush/"
                            ><span class="menu-title">Single Brush</span></a
                          >
                        </li>
                      </ul>
                    </li>
                    <li
                      id="menu-item-6707"
                      class="menu-item menu-item-type-taxonomy menu-item-object-product_cat current-menu-item menu-item-6707"
                    >
                      <a
                        class="elementor-item"
                        href="https://zibabeauty.in/index.php/product-category/sponges-applicators/"
                        ><span class="menu-title"
                          >Sponges &amp; Applicators</span
                        ></a
                      >
                    </li>
                  </ul>
                </li>
                <li
                  id="menu-item-6699"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6699"
                >
                  <a
                    class="elementor-item"
                    href="https://zibabeauty.in/index.php/faq/"
                    ><span class="menu-title">FAQ</span></a
                  >
                </li>
                <li
                  id="menu-item-6698"
                  class="menu-item menu-item-type-post_type menu-item-object-page menu-item-6698"
                >
                  <a
                    class="elementor-item"
                    href="https://zibabeauty.in/index.php/contact-us/"
                    ><span class="menu-title">Contact Us</span></a
                  >
                </li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
      <div class="topbar-device-mobile d-xl-none clearfix">
        <div class="active-mobile">
          <a href="#tbay-mobile-menu-navbar" class="btn btn-sm"
            ><i class="tb-icon tb-icon-menu"></i></a
          ><a href="#page" class="btn btn-sm"
            ><i class="tb-icon tb-icon-cross"></i
          ></a>
        </div>
        <div class="mobile-logo">
          <a href="https://zibabeauty.in/"
            ><img
              src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20579'%3E%3C/svg%3E"
              width="1200"
              height="579"
              alt="Ziba"
              data-lazy-src="https://zibabeauty.in/wp-content/uploads/2021/10/ZibaLogo-for-website-white.png" /><noscript
              ><img
                src="https://zibabeauty.in/wp-content/uploads/2021/10/ZibaLogo-for-website-white.png"
                width="1200"
                height="579"
                alt="Ziba" /></noscript
          ></a>
        </div>
        <div class="device-mini_cart top-cart tbay-element-mini-cart">
          <div
            class="tbay-offcanvas-cart sidebar-right offcanvas offcanvas-end"
            id="cart-offcanvas-mobile"
          >
            <div class="offcanvas-header widget-header-cart">
              <div class="header-cart-content">
                <h3 class="widget-title heading-title">Shopping cart</h3>
                <a
                  href="javascript:;"
                  class="offcanvas-close"
                  data-bs-dismiss="offcanvas"
                  aria-label="Close"
                  ><i class="tb-icon tb-icon-cross"></i
                ></a>
              </div>
            </div>
            <div class="offcanvas-body widget_shopping_cart_content">
              <div class="mini_cart_content">
                <div class="mini_cart_inner">
                  <div class="mcart-border">
                    <ul class="cart_empty">
                      <li><span>Your cart is empty</span></li>
                      <li class="total">
                        <a
                          class="button wc-continue"
                          href="https://zibabeauty.in/index.php/shop/"
                          >Continue Shopping<i
                            class="tb-icon tb-icon-angle-right"
                          ></i
                        ></a>
                      </li>
                    </ul>

                    <div class="clearfix"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tbay-topcart">
            <div id="cart-Dgqbb" class="cart-dropdown dropdown">
              <a
                class="dropdown-toggle mini-cart v2"
                data-bs-toggle="offcanvas"
                data-bs-target="#cart-offcanvas-mobile"
                aria-controls="cart-offcanvas-mobile"
                href="javascript:void(0);"
              >
                <i class="tb-icon tb-icon-cart"></i>
                <span class="mini-cart-items"> 0 </span>
                <span>Cart</span>
              </a>
            </div>
          </div>
        </div>
        <div class="search-device">
          <div class="tbay-search-form tbay-search-mobile">
            <form
              action="https://zibabeauty.in/"
              method="get"
              data-parents=".topbar-device-mobile"
              class="searchform hara-ajax-search show-category"
              data-appendto=".search-results-FbxLx"
              data-subtitle="1"
              data-thumbnail="1"
              data-price="1"
              data-minChars="2"
              data-post-type="product"
              data-count="5"
            >
              <div class="form-group">
                <div class="input-group">
                  <div class="select-category input-group-addon">
                    <select
                      name="product_cat"
                      id="product-cat-FbxLx"
                      class="dropdown_product_cat"
                    >
                      <option value="">All</option>
                      <option class="level-0" value="disposable-brushes">
                        Disposable Brushes&nbsp;&nbsp;(3)
                      </option>
                      <option class="level-0" value="makeup-brushes">
                        Makeup Brushes&nbsp;&nbsp;(73)
                      </option>
                      <option class="level-1" value="brush-set">
                        &nbsp;&nbsp;&nbsp;Brush Set&nbsp;&nbsp;(12)
                      </option>
                      <option class="level-1" value="classic-collection">
                        &nbsp;&nbsp;&nbsp;Classic Collection&nbsp;&nbsp;(41)
                      </option>
                      <option class="level-1" value="metallic-collection">
                        &nbsp;&nbsp;&nbsp;Metallic Collection&nbsp;&nbsp;(7)
                      </option>
                      <option class="level-1" value="professional-brushes">
                        &nbsp;&nbsp;&nbsp;Professional Brushes&nbsp;&nbsp;(25)
                      </option>
                      <option class="level-1" value="single-brush">
                        &nbsp;&nbsp;&nbsp;Single Brush&nbsp;&nbsp;(60)
                      </option>
                      <option
                        class="level-0"
                        value="sponges-applicators"
                        selected="selected"
                      >
                        Sponges &amp; Applicators&nbsp;&nbsp;(8)
                      </option>
                    </select>
                  </div>
                  <div class="button-group input-group-addon">
                    <button type="submit" class="button-search btn btn-sm>">
                      <i aria-hidden="true" class="tb-icon tb-icon-search"></i>
                    </button>
                    <div class="tbay-preloader"></div>
                  </div>
                  <input
                    data-style="right"
                    type="text"
                    placeholder="Search "
                    name="s"
                    required
                    oninvalid="this.setCustomValidity('Enter at least 2 characters')"
                    oninput="setCustomValidity('')"
                    class="tbay-search form-control input-sm"
                  />

                  <div class="search-results-wrapper">
                    <div
                      class="hara-search-results search-results-FbxLx"
                      data-ajaxsearch="1"
                      data-price="1"
                    ></div>
                  </div>
                  <input
                    type="hidden"
                    name="post_type"
                    value="product"
                    class="post_type"
                  />
                </div>
              </div>
            </form>
            <div id="search-mobile-nav-cover"></div>
          </div>
        </div>
      </div>

      <div class="footer-device-mobile d-xl-none clearfix">
        <div class="list-menu-icon">
          <div class="menu-icon">
            <a title="Home" class="home" href="https://zibabeauty.in"
              ><span class="menu-icon-child"
                ><i class="tb-icon tb-icon-home3"></i><span>Home</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Shop"
              class="shop"
              href="https://zibabeauty.in/index.php/shop/"
              ><span class="menu-icon-child"
                ><i class="tb-icon tb-icon-store"></i><span>Shop</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Checkout"
              class="checkout"
              href="https://zibabeauty.in/index.php/checkout/"
              ><span class="menu-icon-child"
                ><i class="icon- icon-credit-card"></i
                ><span>Checkout</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Wishlist"
              class="wishlist"
              href="https://zibabeauty.in/index.php/wishlist/"
              ><span class="menu-icon-child"
                ><i class="icon- icon-heart"></i
                ><span class="count count_wishlist"><span>0</span></span
                ><span>Wishlist</span></span
              ></a
            >
          </div>
          <div class="menu-icon">
            <a
              title="Account"
              class="account"
              href="https://zibabeauty.in/index.php/my-account/"
              ><span class="menu-icon-child"
                ><i class="tb-icon tb-icon-account"></i
                ><span>Account</span></span
              ></a
            >
          </div>
        </div>
      </div>
      <div id="tbay-main-content">
        <div id="main-wrapper" class="shop-left main-wrapper">
          <div id="main-container" class="container">
            <div class="row row-shop-sidebar">
              <aside
                id="sidebar-shop"
                class="sidebar d-none d-xl-block tbay-sidebar-shop col-12 col-xl-3"
              >
                <aside
                  id="woocommerce_product_categories-1"
                  class="widget woocommerce widget_product_categories"
                >
                  <h2 class="widget-title">Product Categories</h2>
                  <ul class="product-categories">
                    @foreach($cate as $item)
                    <li class="menu-item">
                        <a class="elementor-item" href="{{ url('/category/' . $item->id) }}">
                            <span class="menu-title">{{ $item->name }}</span>
                        </a>
                    </li>
               @endforeach
                    <li class="cat-item cat-item-15">
                      <a
                        href="#"
                        >Uncategorized</a
                      >
                    </li>
                   
                  </ul>
                </aside>
                <aside
                  id="woocommerce_products-1"
                  class="widget woocommerce widget_products"
                >
                  <h2 class="widget-title">Products</h2>
                  <ul class="product_list_widget">
                    @foreach($category->products as $product)
                    <li class="product-block vertical">
                      <div class="product-content">
                        <div class="block-inner">
                          <figure class="image">
                            <a
                              href="{{url('/product/'. $product->id)}}"
                            >
                              <img
                                width="406"
                                height="406"
                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20406%20406'%3E%3C/svg%3E"
                                class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
                                alt=""
                                decoding="async"
                                data-lazy-src="{{$product->image}}"
                              /><noscript
                                ><img
                                  width="406"
                                  height="406"
                                  src="{{$product->image}}"
                                  class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
                                  alt=""
                                  decoding="async"
                              /></noscript>
                            </a>
                          </figure>
                        </div>
                        <div class="caption">
                            
                          <h3 class="name full_name">
                            <a
                              href="{{url('/product/'.$product->id)}}"
                              >{{$product->name}}</a
                            >
                          </h3>

                          <span class="woocommerce-Price-amount amount"
                            ><bdi
                              ><span class="woocommerce-Price-currencySymbol"
                                >&#8377;</span
                              >{{$product->current_price}}</bdi
                            ></span
                          >
                         
                        </div>
                      </div>
                    </li>
                    @endforeach
                  </ul>
                </aside>
                <aside id="tag_cloud-1" class="widget widget_tag_cloud">
                  <h2 class="widget-title">Tag Cloud</h2>
                  <div class="tagcloud">
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-24 tag-link-position-1"
                      style="font-size: 19.2pt"
                      aria-label="Anti-Ageing (5 items)"
                      >Anti-Ageing</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-25 tag-link-position-2"
                      style="font-size: 19.2pt"
                      aria-label="Beauty (5 items)"
                      >Beauty</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-26 tag-link-position-3"
                      style="font-size: 12.2pt"
                      aria-label="Cleansers &amp; Toners (2 items)"
                      >Cleansers &amp; Toners</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-27 tag-link-position-4"
                      style="font-size: 15pt"
                      aria-label="Exfoliators (3 items)"
                      >Exfoliators</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-28 tag-link-position-5"
                      style="font-size: 19.2pt"
                      aria-label="Gifts (5 items)"
                      >Gifts</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-29 tag-link-position-6"
                      style="font-size: 20.833333333333pt"
                      aria-label="Makeup (6 items)"
                      >Makeup</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-30 tag-link-position-7"
                      style="font-size: 22pt"
                      aria-label="Men (7 items)"
                      >Men</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-31 tag-link-position-8"
                      style="font-size: 19.2pt"
                      aria-label="Moisturisers (5 items)"
                      >Moisturisers</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-32 tag-link-position-9"
                      style="font-size: 8pt"
                      aria-label="Oils &amp; Serums (1 item)"
                      >Oils &amp; Serums</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-33 tag-link-position-10"
                      style="font-size: 8pt"
                      aria-label="Skincare (1 item)"
                      >Skincare</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-34 tag-link-position-11"
                      style="font-size: 12.2pt"
                      aria-label="Skincare Treatments (2 items)"
                      >Skincare Treatments</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-35 tag-link-position-12"
                      style="font-size: 12.2pt"
                      aria-label="Tools (2 items)"
                      >Tools</a
                    >
                    <a
                      href="#"
                      class="tag-cloud-link tag-link-36 tag-link-position-13"
                      style="font-size: 19.2pt"
                      aria-label="Valentine (5 items)"
                      >Valentine</a
                    >
                    <a
                      href="#   "
                      class="tag-cloud-link tag-link-37 tag-link-position-14"
                      style="font-size: 20.833333333333pt"
                      aria-label="Women (6 items)"
                      >Women</a
                    >
                  </div>
                </aside>
              </aside>

              <div
                id="main"
                class="archive-shop col-12 col-xl-9 content col-12"
              >
                <!-- .content -->

                <header class="woocommerce-products-header"></header>

                <div style="position: relative">
                  <a
                    href="javascript:void(0);"
                    class="woof_show_auto_form woof_btn_default"
                  ></a
                  ><br />
                  <!-------------------- inline css for js anim ----------------------->
                  <div
                    class="woof_auto_show woof_overflow_hidden"
                    style="opacity: 0; height: 1px"
                  >
                    <div class="woof_auto_show_indent woof_overflow_hidden">
                      <div
                        class="woof woof_sid woof_sid_flat_white woof_auto_1_columns"
                        data-sid="flat_white woof_auto_1_columns"
                        data-shortcode="woof sid=&#039;flat_white woof_auto_1_columns&#039; autohide=&#039;1&#039; price_filter=&#039;1&#039; "
                        data-redirect=""
                        data-autosubmit="1"
                        data-ajax-redraw="0"
                      >
                        <!--- here is possible to drop html code which is never redraws by AJAX ---->

                        <div class="woof_redraw_zone" data-woof-ver="3.3.2">
                          <div
                            data-css-class="woof_text_search_container"
                            class="woof_text_search_container woof_container woof_container_woof_text"
                          >
                            <div class="woof_container_overlay_item"></div>
                            <div class="woof_container_inner">
                              <a
                                href="javascript:void(0);"
                                class="woof_text_search_go"
                              ></a>
                              <label
                                class="woof_wcga_label_hide"
                                for="woof_txt_search67f698d8e7966"
                                >Text search</label
                              >
                              <input
                                type="search"
                                class="woof_husky_txt-input"
                                id="woof_txt_search67f698d8e7966"
                                placeholder=" "
                                data-behavior="title_or_content_or_excerpt"
                                data-search_by_full_word="0"
                                data-autocomplete="1"
                                data-how_to_open_links="0"
                                data-taxonomy_compatibility="0"
                                data-sku_compatibility="1"
                                data-custom_fields=""
                                data-search_desc_variant="0"
                                data-view_text_length="10"
                                data-min_symbols="3"
                                data-max_posts="10"
                                data-image=""
                                data-template=""
                                data-max_open_height="300"
                                data-page="0"
                                value=""
                                autocomplete="off"
                              />
                            </div>
                          </div>
                          <div
                            data-css-class="woof_container_product_tag"
                            class="woof_container woof_container_mselect woof_container_product_tag woof_container_4 woof_container_producttags"
                          >
                            <div class="woof_container_overlay_item"></div>
                            <div
                              class="woof_container_inner woof_container_inner_producttags"
                            >
                              <h4>Product Tags</h4>
                              <div class="woof_block_html_items">
                                <label
                                  class="woof_wcga_label_hide"
                                  for="woof_tax_mselect_product_tag"
                                  >Product Tags</label
                                >
                                <select
                                  id="woof_tax_mselect_product_tag"
                                  class="woof_mselect woof_mselect_product_tag"
                                  data-placeholder="Product Tags"
                                  multiple=""
                                  size="1"
                                  name="product_tag"
                                >
                                  <option value="0"></option>
                                  <option disabled="" value="accessories">
                                    Accessories (0)
                                  </option>
                                  <option disabled="" value="anti-ageing">
                                    Anti-Ageing (0)
                                  </option>
                                  <option disabled="" value="bags">
                                    Bags (0)
                                  </option>
                                  <option disabled="" value="cleansers-toners">
                                    Cleansers &amp; Toners (0)
                                  </option>
                                  <option disabled="" value="exfoliators">
                                    Exfoliators (0)
                                  </option>
                                  <option value="eye-brush">
                                    eye brush (2)
                                  </option>
                                  <option value="eyeshadow">
                                    eyeshadow (1)
                                  </option>
                                  <option disabled="" value="gadgets">
                                    Gadgets (0)
                                  </option>
                                  <option value="makeup-brush">
                                    makeup brush (1)
                                  </option>
                                  <option value="makeup-brushe">
                                    makeup brushe (1)
                                  </option>
                                  <option disabled="" value="men">
                                    Men (0)
                                  </option>
                                  <option disabled="" value="moisturisers">
                                    Moisturisers (0)
                                  </option>
                                  <option disabled="" value="oils-serums">
                                    Oils &amp; Serums (0)
                                  </option>
                                  <option disabled="" value="skincare">
                                    Skincare (0)
                                  </option>
                                  <option
                                    disabled=""
                                    value="skincare-treatments"
                                  >
                                    Skincare Treatments (0)
                                  </option>
                                  <option disabled="" value="tools">
                                    Tools (0)
                                  </option>
                                  <option disabled="" value="women">
                                    Women (0)
                                  </option>
                                </select>

                                <input
                                  type="hidden"
                                  value="Accessories"
                                  data-anchor="woof_n_product_tag_accessories"
                                />
                                <input
                                  type="hidden"
                                  value="Anti-Ageing"
                                  data-anchor="woof_n_product_tag_anti-ageing"
                                />
                                <input
                                  type="hidden"
                                  value="Bags"
                                  data-anchor="woof_n_product_tag_bags"
                                />
                                <input
                                  type="hidden"
                                  value="Cleansers &amp; Toners"
                                  data-anchor="woof_n_product_tag_cleansers-toners"
                                />
                                <input
                                  type="hidden"
                                  value="Exfoliators"
                                  data-anchor="woof_n_product_tag_exfoliators"
                                />
                                <input
                                  type="hidden"
                                  value="eye brush"
                                  data-anchor="woof_n_product_tag_eye-brush"
                                />
                                <input
                                  type="hidden"
                                  value="eyeshadow"
                                  data-anchor="woof_n_product_tag_eyeshadow"
                                />
                                <input
                                  type="hidden"
                                  value="Gadgets"
                                  data-anchor="woof_n_product_tag_gadgets"
                                />
                                <input
                                  type="hidden"
                                  value="makeup brush"
                                  data-anchor="woof_n_product_tag_makeup-brush"
                                />
                                <input
                                  type="hidden"
                                  value="makeup brushe"
                                  data-anchor="woof_n_product_tag_makeup-brushe"
                                />
                                <input
                                  type="hidden"
                                  value="Men"
                                  data-anchor="woof_n_product_tag_men"
                                />
                                <input
                                  type="hidden"
                                  value="Moisturisers"
                                  data-anchor="woof_n_product_tag_moisturisers"
                                />
                                <input
                                  type="hidden"
                                  value="Oils &amp; Serums"
                                  data-anchor="woof_n_product_tag_oils-serums"
                                />
                                <input
                                  type="hidden"
                                  value="Skincare"
                                  data-anchor="woof_n_product_tag_skincare"
                                />
                                <input
                                  type="hidden"
                                  value="Skincare Treatments"
                                  data-anchor="woof_n_product_tag_skincare-treatments"
                                />
                                <input
                                  type="hidden"
                                  value="Tools"
                                  data-anchor="woof_n_product_tag_tools"
                                />
                                <input
                                  type="hidden"
                                  value="Women"
                                  data-anchor="woof_n_product_tag_women"
                                />
                              </div>

                              <input
                                type="hidden"
                                name="woof_t_product_tag"
                                value="Product tags"
                              /><!-- for red button search nav panel -->
                            </div>
                          </div>

                          <div class="woof_submit_search_form_container"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="woof_products_top_panel_content"></div>
                <div class="woof_products_top_panel"></div>
                <div class="tbay-filter">
                  <div class="woocommerce-notices-wrapper"></div>
                  <div
                    class="main-filter d-flex justify-content-end filter-vendor"
                  >
                    <p class="woocommerce-result-count">
                      Showing all 8 results
                    </p>
                    <div class="filter-btn-wrapper d-xl-none">
                      <button
                        id="button-filter-btn"
                        class="button-filter-btn hidden-lg hidden-md"
                        type="submit"
                      >
                        <i
                          class="tb-icon tb-icon-filter"
                          aria-hidden="true"
                        ></i>
                      </button>
                    </div>
                    <div id="filter-close"></div>
                    <div class="tbay-ordering">
                      <span>Sort by:</span>
                      <form class="woocommerce-ordering" method="get">
                        <select
                          name="orderby"
                          class="orderby"
                          aria-label="Shop order"
                        >
                          <option value="menu_order" selected="selected">
                            Default sorting
                          </option>
                          <option value="popularity">Sort by popularity</option>
                          <option value="rating">Sort by average rating</option>
                          <option value="date">Sort by latest</option>
                          <option value="price">
                            Sort by price: low to high
                          </option>
                          <option value="price-desc">
                            Sort by price: high to low
                          </option>
                        </select>
                        <input type="hidden" name="paged" value="1" />
                      </form>
                    </div>
                    <div class="display-mode-warpper">
                      <span> View: </span>
                      <a
                        href="javascript:void(0);"
                        id="display-mode-list"
                        class="display-mode-btn list"
                        title="List"
                        ><i class="fas fa-list"></i
                      ></a>
                      <a
                        href="javascript:void(0);"
                        id="display-mode-grid"
                        class="display-mode-btn active"
                        title="Grid"
                        ><i class="fas fa-th-list"></i
                      ></a>
                    </div>
                  </div>
                </div>
                <div class="display-products products products-grid">
                  <div
                    class="row"
                    data-xlgdesktop="4"
                    data-desktop="4"
                    data-desktopsmall="4"
                    data-tablet="3"
                    data-landscape="3"
                    data-mobile="2"
                  >
                  @foreach($category->products as $product)
                    <div
                      class="product type-product post-6011 status-publish first instock product_cat-sponges-applicators has-post-thumbnail sale taxable shipping-taxable purchasable product-type-variable"
                    >
                      <div
                        class="product-block grid product v1 tbay-variable-sale"
                        data-product-id="6011"
                      >
                     
                        <div class="product-content">
                            
                          <div class="block-inner">
                            <figure class="image">
                              <a
                                title="{{ $product->name }} &#8211; Black &#8211; Gourd Shape"
                                href="#"
                                class="product-image"
                              >
                                <img
                                  width="406"
                                  height="406"
                                  src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20406%20406'%3E%3C/svg%3E"
                                  class="attachment-shop_catalog image-effect"
                                  alt=""
                                  decoding="async"
                                  data-lazy-src="{{ $product->image }}"
                                /><noscript
                                  ><img
                                    width="406"
                                    height="406"
                                    src="{{ $product->image }}"
                                    class="attachment-shop_catalog image-effect"
                                    alt=""
                                    decoding="async" /></noscript
                                ><img
                                  width="406"
                                  height="406"
                                  src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20406%20406'%3E%3C/svg%3E"
                                  class="image-hover"
                                  alt=""
                                  decoding="async"
                                  data-lazy-src="{{ $product->image }}"
                                /><noscript
                                  ><img
                                    width="406"
                                    height="406"
                                    src="{{ $product->image }}"
                                    class="image-hover"
                                    alt=""
                                    decoding="async"
                                /></noscript>
                              </a>
                            </figure>
                            <div class="group-buttons">
                              <div
                                class="button-wishlist shown-mobile"
                                title="Wishlist"
                              >
                                <div
                                  class="yith-wcwl-add-to-wishlist add-to-wishlist-6011 wishlist-fragment on-first-load"
                                  data-fragment-ref="6011"
                                  data-fragment-options='{"base_url":"","in_default_wishlist":false,"is_single":false,"show_exists":false,"product_id":6011,"parent_product_id":6011,"product_type":"variable","show_view":false,"browse_wishlist_text":"View Wishlist","already_in_wishslist_text":"","product_added_text":"","heading_icon":"custom","available_multi_wishlist":false,"disable_wishlist":false,"show_count":false,"ajax_loading":false,"loop_position":"after_add_to_cart","item":"add_to_wishlist"}'
                                >
                                  <!-- ADD TO WISHLIST -->

                                  <div class="yith-wcwl-add-button">
                                    <a
                                      href="?add_to_wishlist=6011&#038;_wpnonce=f3b37aaca7"
                                      class="add_to_wishlist single_add_to_wishlist"
                                      data-product-id="6011"
                                      data-product-type="variable"
                                      data-original-product-id="6011"
                                      data-title="Add to wishlist"
                                      rel="nofollow"
                                    >
                                      <i class="fas fa-heart"></i>
                                      <span>Add to wishlist</span>
                                    </a>
                                  </div>

                                  <!-- COUNT TEXT -->
                                </div>
                              </div>
                              <div class="tbay-quick-view">
                                <a
                                  href="#"
                                  class="qview-button"
                                  title="Quick View"
                                  data-effect="mfp-move-from-top"
                                  data-product_id="6011"
                                >
                                  <i class="fas fa-eye"></i>
                                  <span>Quick View</span>
                                </a>
                              </div>
                            </div>
                            <div class="group-add-to-cart">
                              <div class="add-cart" title="Select options">
                                <a
                                  href="#"
                                  data-quantity="1"
                                  class="button product_type_variable add_to_cart_button"
                                  data-product_id="6011"
                                  data-product_sku=""
                                  aria-label="Select options for &ldquo;Makeup Sponge - Black - Gourd Shape&rdquo;"
                                  aria-describedby="This product has multiple variants. The options may be chosen on the product page"
                                  rel="nofollow"
                                  ><span class="title-cart"
                                    >Select options</span
                                  ></a
                                >
                              </div>
                            </div>
                            
                          </div>

                          <span class="onsale"
                            ><span class="saled">Sale</span></span
                          >

                          <div class="caption">
                            <div class="group-content"></div>

                            <h3 class="name full_name">
                              <a
                                href="{{url('/product/'.$item->id)}}"
                                >{{ $product->name }}</a
                              >
                            </h3>
                            <div class="tbay-subtitle">Pack Of 2</div>

                            <span class="price"
                              ><del aria-hidden="true"
                                ><span class="woocommerce-Price-amount amount"
                                  ><bdi
                                    ><span
                                      class="woocommerce-Price-currencySymbol"
                                      >&#8377;</span
                                    >{{ $product->original_price }}</bdi
                                  ></span
                                ></del
                              >
                              <ins
                                ><span class="woocommerce-Price-amount amount"
                                  ><bdi
                                    ><span
                                      class="woocommerce-Price-currencySymbol"
                                      >&#8377;</span
                                    >{{ $product->current_price }}</bdi
                                  ></span
                                ></ins
                              ></span
                            >
                          </div>
                          
                         </div>
                         
                         </div>
                           </div>
                           @endforeach
                        
                      </div>
                    </div>
                  </div>
                </div>
                <aside
                  id="sidebar-bottom-archive"
                  class="sidebar bottom-archive-content"
                >
                  <style id="elementor-post-1593">
                    .elementor-1593
                      .elementor-element.elementor-element-3a82185
                      > .elementor-container
                      > .elementor-column
                      > .elementor-widget-wrap {
                      align-content: center;
                      align-items: center;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-3a82185:not(.elementor-motion-effects-element-type-background),
                    .elementor-1593
                      .elementor-element.elementor-element-3a82185
                      > .elementor-motion-effects-container
                      > .elementor-motion-effects-layer {
                      background-image: url("https://zibabeauty.in/wp-content/uploads/2021/11/banner-bottom-shop-7.png");
                      background-position: center center;
                      background-repeat: no-repeat;
                      background-size: cover;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-3a82185 {
                      transition: background 0.3s, border 0.3s,
                        border-radius 0.3s, box-shadow 0.3s;
                      padding: 15px 59px 16px 22px;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-3a82185
                      > .elementor-background-overlay {
                      transition: background 0.3s, border-radius 0.3s,
                        opacity 0.3s;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-aef1d48
                      .elementor-icon-list-icon
                      i {
                      color: #007d71;
                      transition: color 0.3s;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-aef1d48
                      .elementor-icon-list-icon
                      svg {
                      fill: #007d71;
                      transition: fill 0.3s;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-aef1d48 {
                      --e-icon-list-icon-size: 22px;
                      --e-icon-list-icon-align: left;
                      --e-icon-list-icon-margin: 0
                        calc(var(--e-icon-list-icon-size, 1em) * 0.25) 0 0;
                      --icon-vertical-offset: 0px;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-aef1d48
                      .elementor-icon-list-item
                      > .elementor-icon-list-text,
                    .elementor-1593
                      .elementor-element.elementor-element-aef1d48
                      .elementor-icon-list-item
                      > a {
                      font-size: 18px;
                      font-weight: 400;
                      line-height: 27px;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-aef1d48
                      .elementor-icon-list-text {
                      color: #007d71;
                      transition: color 0.3s;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-a32b1cc
                      .elementor-button
                      .elementor-align-icon-right {
                      margin-left: 16px;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-a32b1cc
                      .elementor-button
                      .elementor-align-icon-left {
                      margin-right: 16px;
                    }
                    .elementor-1593
                      .elementor-element.elementor-element-a32b1cc
                      .elementor-button {
                      font-size: 16px;
                      line-height: 16px;
                      background-color: #007d71;
                      border-radius: 3px 3px 3px 3px;
                      padding: 10px 16px 10px 16px;
                    }
                    @media (min-width: 768px) {
                      .elementor-1593
                        .elementor-element.elementor-element-79498c8 {
                        width: 86.431%;
                      }
                      .elementor-1593
                        .elementor-element.elementor-element-d8bc2d3 {
                        width: 13.532%;
                      }
                    }
                    @media (max-width: 767px) {
                      .elementor-1593
                        .elementor-element.elementor-element-3a82185:not(.elementor-motion-effects-element-type-background),
                      .elementor-1593
                        .elementor-element.elementor-element-3a82185
                        > .elementor-motion-effects-container
                        > .elementor-motion-effects-layer {
                        background-position: 56% 0px;
                      }
                      .elementor-1593
                        .elementor-element.elementor-element-3a82185 {
                        padding: 15px 15px 16px 15px;
                      }
                      .elementor-1593
                        .elementor-element.elementor-element-aef1d48 {
                        --e-icon-list-icon-align: left;
                        --e-icon-list-icon-margin: 0
                          calc(var(--e-icon-list-icon-size, 1em) * 0.25) 0 0;
                      }
                      .elementor-1593
                        .elementor-element.elementor-element-a32b1cc
                        > .elementor-widget-container {
                        margin: 15px 0px 0px 0px;
                      }
                    }
                  </style>
                  <div
                    data-elementor-type="wp-post"
                    data-elementor-id="1593"
                    class="elementor elementor-1593"
                  >
                    <section
                      class="elementor-section elementor-top-section elementor-element elementor-element-3a82185 elementor-section-content-middle elementor-section-boxed elementor-section-height-default elementor-section-height-default"
                      data-id="3a82185"
                      data-element_type="section"
                      data-settings='{"background_background":"classic"}'
                    >
                      <div
                        class="elementor-container elementor-column-gap-default"
                      >
                        <div
                          class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-79498c8"
                          data-id="79498c8"
                          data-element_type="column"
                        >
                          <div
                            class="elementor-widget-wrap elementor-element-populated"
                          >
                            <div
                              class="elementor-element elementor-element-aef1d48 elementor-align-left elementor-mobile-align-left elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                              data-id="aef1d48"
                              data-element_type="widget"
                              data-widget_type="icon-list.default"
                            >
                              <div class="elementor-widget-container">
                                <link
                                  rel="stylesheet"
                                  href="https://zibabeauty.in/wp-content/plugins/elementor/assets/css/widget-icon-list.min.css"
                                />
                                <ul class="elementor-icon-list-items">
                                  <li class="elementor-icon-list-item">
                                    <span class="elementor-icon-list-icon">
                                      <i
                                        aria-hidden="true"
                                        class="tb-icon tb-icon-email"
                                      ></i>
                                    </span>
                                    <span class="elementor-icon-list-text"
                                      ><strong>100% Secure delivery</strong>
                                      without contacting the courier</span
                                    >
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div
                          class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-d8bc2d3"
                          data-id="d8bc2d3"
                          data-element_type="column"
                        >
                          <div
                            class="elementor-widget-wrap elementor-element-populated"
                          >
                            <div
                              class="elementor-element elementor-element-a32b1cc elementor-align-left elementor-mobile-align-center elementor-widget elementor-widget-button"
                              data-id="a32b1cc"
                              data-element_type="widget"
                              data-widget_type="button.default"
                            >
                              <div class="elementor-widget-container">
                                <div class="elementor-button-wrapper">
                                  <a
                                    class="elementor-button elementor-button-link elementor-size-sm"
                                    href="#"
                                  >
                                    <span
                                      class="elementor-button-content-wrapper"
                                    >
                                      <span
                                        class="elementor-button-icon elementor-align-icon-right"
                                      >
                                        <i
                                          aria-hidden="true"
                                          class="tb-icon tb-icon-logout"
                                        ></i>
                                      </span>
                                      <span class="elementor-button-text"
                                        >More</span
                                      >
                                    </span>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </section>
                  </div>
                </aside>
              </div>
              <!-- .content -->
            </div>
            <!-- .row -->
          </div>
          <!-- container -->
        </div>
        <!-- main wrapper-->
      </div>
      <!-- .site-content -->

      @endsection